<?php

namespace App\Services\Cybersource;

use Illuminate\Support\Facades\Cache;

/**
 * Verifies the signature on a JWT issued by Cybersource.
 *
 * When the complete mandate runs, the browser hands back the outcome of a real
 * transaction. That outcome decides whether a registration is marked paid, so
 * it cannot be taken on trust: the token is signed RS256 with a key Cybersource
 * publishes, and the claims are only read once that signature checks out.
 *
 * The key is fetched from /flex/v2/public-keys/{kid} in JWK form — a modulus
 * and exponent — which openssl cannot use directly, so it is re-encoded as a
 * DER SubjectPublicKeyInfo structure and wrapped as PEM.
 */
class JwtVerifier
{
    /** How long a fetched public key is reused before it is looked up again. */
    private const KEY_CACHE_SECONDS = 3600;

    public function __construct(private CybersourceClient $client)
    {
    }

    /**
     * @return array<string, mixed> the claims, once the signature is verified
     *
     * @throws CybersourceException
     */
    public function verify(string $jwt): array
    {
        $parts = explode('.', trim($jwt));

        if (count($parts) !== 3) {
            throw new CybersourceException('The payment result was not a well-formed token.');
        }

        [$header, $payload, $signature] = $parts;

        $claimsHeader = $this->decodeSegment($header);

        // Only RS256 is accepted. Reading `alg` and trusting it would let a
        // caller present an unsigned ("none") token as a verified one.
        if (($claimsHeader['alg'] ?? null) !== 'RS256') {
            throw CybersourceException::withContext(
                'The payment result was signed with an unexpected algorithm.',
                ['alg' => $claimsHeader['alg'] ?? null]
            );
        }

        $kid = $claimsHeader['kid'] ?? null;

        if (! is_string($kid) || $kid === '') {
            throw new CybersourceException('The payment result did not name a signing key.');
        }

        $verified = openssl_verify(
            $header . '.' . $payload,
            $this->base64UrlDecode($signature),
            $this->publicKey($kid),
            OPENSSL_ALGO_SHA256
        );

        if ($verified !== 1) {
            throw CybersourceException::withContext(
                'The payment result failed signature verification.',
                ['kid' => $kid]
            );
        }

        return $this->decodeSegment($payload);
    }

    /**
     * The PEM public key for one key id.
     *
     * Cached because every payment verifies against the same handful of keys,
     * and a lookup would otherwise sit in the middle of the checkout response.
     *
     * @throws CybersourceException
     */
    private function publicKey(string $kid): string
    {
        $cached = Cache::get($this->cacheKey($kid));

        if (is_string($cached)) {
            return $cached;
        }

        $response = $this->client->getUnsigned('/flex/v2/public-keys/' . urlencode($kid));

        if (! $response->successful()) {
            throw CybersourceException::withContext(
                'The signing key for the payment result could not be retrieved.',
                ['kid' => $kid, 'status' => $response->status()]
            );
        }

        $pem = $this->pemFromJwk($response->json() ?? []);

        Cache::put($this->cacheKey($kid), $pem, self::KEY_CACHE_SECONDS);

        return $pem;
    }

    private function cacheKey(string $kid): string
    {
        return 'cybersource:public-key:' . sha1($this->client->host() . '|' . $kid);
    }

    /**
     * Turn a JWK into a PEM-encoded RSA public key.
     *
     * @param  array<string, mixed>  $jwk
     *
     * @throws CybersourceException
     */
    private function pemFromJwk(array $jwk): string
    {
        if (($jwk['kty'] ?? null) !== 'RSA' || empty($jwk['n']) || empty($jwk['e'])) {
            throw CybersourceException::withContext(
                'The signing key was not a usable RSA key.',
                ['kty' => $jwk['kty'] ?? null]
            );
        }

        $rsaPublicKey = $this->derSequence(
            $this->derInteger($this->base64UrlDecode((string) $jwk['n']))
            . $this->derInteger($this->base64UrlDecode((string) $jwk['e']))
        );

        // AlgorithmIdentifier: OID 1.2.840.113549.1.1.1 (rsaEncryption), NULL
        // parameters. Constant for every RSA key, so it is written out as-is.
        $algorithm = (string) hex2bin('300d06092a864886f70d0101010500');

        $der = $this->derSequence($algorithm . $this->derBitString($rsaPublicKey));

        return "-----BEGIN PUBLIC KEY-----\n"
            . chunk_split(base64_encode($der), 64, "\n")
            . "-----END PUBLIC KEY-----\n";
    }

    private function derSequence(string $contents): string
    {
        return "\x30" . $this->derLength(strlen($contents)) . $contents;
    }

    /**
     * A DER INTEGER. Leading zero bytes are dropped, then one is put back when
     * the top bit is set, since DER integers are signed and the modulus is not.
     */
    private function derInteger(string $bytes): string
    {
        $bytes = ltrim($bytes, "\x00");

        if ($bytes === '') {
            $bytes = "\x00";
        }

        if (ord($bytes[0]) > 0x7f) {
            $bytes = "\x00" . $bytes;
        }

        return "\x02" . $this->derLength(strlen($bytes)) . $bytes;
    }

    /** A DER BIT STRING, whose first content byte counts unused trailing bits. */
    private function derBitString(string $contents): string
    {
        $contents = "\x00" . $contents;

        return "\x03" . $this->derLength(strlen($contents)) . $contents;
    }

    private function derLength(int $length): string
    {
        if ($length < 0x80) {
            return chr($length);
        }

        $bytes = ltrim(pack('N', $length), "\x00");

        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeSegment(string $segment): array
    {
        $json = json_decode($this->base64UrlDecode($segment), true);

        return is_array($json) ? $json : [];
    }

    private function base64UrlDecode(string $value): string
    {
        $padding = strlen($value) % 4;

        if ($padding > 0) {
            $value .= str_repeat('=', 4 - $padding);
        }

        return (string) base64_decode(strtr($value, '-_', '+/'), true);
    }
}
