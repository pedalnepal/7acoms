<?php

namespace App\Services\Cybersource;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Signs and sends requests to the Cybersource REST API.
 *
 * Cybersource authenticates each call with an HTTP Signature: a set of headers
 * is canonicalised into a signing string, then signed with HMAC-SHA256 using
 * the REST shared secret. The secret arrives base64-encoded and must be decoded
 * before use.
 */
class CybersourceClient
{
    public function __construct(private array $config)
    {
    }

    public function host(): string
    {
        $environment = $this->config['environment'] === 'production' ? 'production' : 'test';

        return $this->config['hosts'][$environment];
    }

    public function isConfigured(): bool
    {
        return ! empty($this->config['merchant_id'])
            && ! empty($this->config['api_key_id'])
            && ! empty($this->config['api_secret_key']);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws CybersourceException
     */
    public function post(string $path, array $payload): Response
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);

        return $this->send('POST', $path, $body);
    }

    /**
     * @throws CybersourceException
     */
    public function get(string $path): Response
    {
        return $this->send('GET', $path);
    }

    /**
     * A GET that carries no merchant signature.
     *
     * The public-key endpoint serves keys anyone may fetch, and signing the
     * request is not merely unnecessary — the endpoint is not scoped to a
     * merchant, so an unexpected signature header only invites a rejection.
     *
     * @throws CybersourceException
     */
    public function getUnsigned(string $path): Response
    {
        $url = 'https://' . $this->host() . $path;

        try {
            return Http::withHeaders(['Accept' => 'application/json'])
                ->timeout((int) ($this->config['timeout'] ?? 30))
                ->get($url);
        } catch (\Throwable $e) {
            throw new CybersourceException(
                'Could not reach the payment gateway: ' . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * @throws CybersourceException
     */
    private function send(string $method, string $path, ?string $body = null): Response
    {
        $this->assertConfigured();

        $url = 'https://' . $this->host() . $path;

        $request = Http::withHeaders($this->signedHeaders($method, $path, $body ?? ''))
            ->timeout((int) ($this->config['timeout'] ?? 30));

        try {
            $response = $method === 'POST'
                ? $request->withBody($body ?? '', 'application/json')->post($url)
                : $request->get($url);
        } catch (\Throwable $e) {
            throw new CybersourceException(
                'Could not reach the payment gateway: ' . $e->getMessage(),
                0,
                $e
            );
        }

        return $response;
    }

    /**
     * Build the authentication headers for one request.
     *
     * The signing string joins the signed headers, in the order named by the
     * Signature header's `headers` parameter, one "name: value" pair per line.
     * The digest covers the request body and is only sent with POSTs.
     *
     * @return array<string, string>
     */
    private function signedHeaders(string $method, string $path, string $body): array
    {
        $host       = $this->host();
        $date       = gmdate('D, d M Y H:i:s \G\M\T');
        $merchantId = $this->config['merchant_id'];

        $headers = [
            'v-c-merchant-id' => $merchantId,
            'Date'            => $date,
            'Host'            => $host,
            // The REST API serves HAL. Asking for plain application/json makes
            // /pts/v2/payments answer 404 "Resource not found" as plain text —
            // the request never reaches validation — while the Sessions API
            // happens to tolerate it. Ask for what the API actually produces.
            'Accept'          => 'application/hal+json;charset=utf-8',
        ];

        $signedNames = ['host', 'date', '(request-target)'];
        $signingRows = [
            'host: ' . $host,
            'date: ' . $date,
            '(request-target): ' . strtolower($method) . ' ' . $path,
        ];

        if (strtoupper($method) === 'POST') {
            $digest = 'SHA-256=' . base64_encode(hash('sha256', $body, true));

            $headers['Digest']       = $digest;
            $headers['Content-Type'] = 'application/json';

            $signedNames[] = 'digest';
            $signingRows[] = 'digest: ' . $digest;
        }

        $signedNames[] = 'v-c-merchant-id';
        $signingRows[] = 'v-c-merchant-id: ' . $merchantId;

        $signature = base64_encode(hash_hmac(
            'sha256',
            implode("\n", $signingRows),
            base64_decode($this->config['api_secret_key']),
            true
        ));

        $headers['Signature'] = sprintf(
            'keyid="%s", algorithm="HmacSHA256", headers="%s", signature="%s"',
            $this->config['api_key_id'],
            implode(' ', $signedNames),
            $signature
        );

        return $headers;
    }

    /**
     * @throws CybersourceException
     */
    private function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new CybersourceException(
                'Cybersource credentials are missing. Set CYBS_MERCHANT_ID, CYBS_API_KEY_ID and CYBS_API_SECRET_KEY.'
            );
        }
    }
}
