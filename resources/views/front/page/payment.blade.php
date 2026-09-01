@extends('front.common.layout')

@section('content')

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="page-banner">
  <img class="page-banner-img" src="{{ asset('images/banner-1.jpg') }}" alt="Oral and Maxillofacial Surgery congress">
  <div class="page-banner-overlay"></div>
  <div class="container">
    <div class="page-banner-body">
      <h1 class="page-banner-title">Complete Your Payment</h1>
      <p class="page-banner-sub">7th ACOMS Trainee Conference 2027, Kathmandu — step 2 of 2.</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <div class="content-block">
      <div class="sec-head">
        <span class="sec-bar"></span>
        <h2>Registration Payment</h2>
        <span class="sec-line"></span>
      </div>

      <p class="content-text lead">
        Your registration has been saved. Pay the fee below to confirm your place — your registration is
        complete as soon as the payment goes through.
      </p>

      @if($error)
        <div class="alert alert-danger mt-3" role="alert" style="border-left:4px solid var(--red);background:#fdecec;color:#842029;padding:1rem 1.25rem;border-radius:6px;">
          <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $error }}
        </div>
      @endif

      <div class="row g-4 mt-1">

        <!-- ============ ORDER SUMMARY ============ -->
        <div class="col-lg-5">
          <div class="form-card h-100">
            <div class="form-section">
              <p class="form-section-title"><i class="fa-solid fa-receipt"></i> Summary</p>

              <dl class="pay-summary">
                <div class="pay-row">
                  <dt>Reference</dt>
                  <dd><strong>{{ $registration->paymentCode() }}</strong></dd>
                </div>
                <div class="pay-row">
                  <dt>Delegate</dt>
                  <dd>{{ $registration->full_name }}</dd>
                </div>
                <div class="pay-row">
                  <dt>Registering For</dt>
                  <dd>{{ $registration->reg_for ?? '—' }}</dd>
                </div>
                <div class="pay-row">
                  <dt>Rate</dt>
                  <dd>{{ $registration->fee_tier ? ucfirst($registration->fee_tier) : '—' }}</dd>
                </div>

                <div class="pay-divider"></div>

                @foreach($registration->fee_breakdown ?? [] as $line)
                  <div class="pay-row">
                    <dt>{{ $line['label'] }}</dt>
                    <dd>{{ $registration->currency }} {{ number_format((float) $line['amount'], 2) }}</dd>
                  </div>
                @endforeach

                <div class="pay-row pay-total">
                  <dt>Total due</dt>
                  <dd>{{ $registration->formattedAmount() }}</dd>
                </div>

                @if($registration->isConverted())
                  {{-- The bank settles in NPR, so this is what the statement
                       will actually read. Saying so here, with the rate,
                       prevents the mismatch turning into a support query. --}}
                  <div class="pay-row pay-total">
                    <dt>Charged as</dt>
                    <dd>{{ $registration->formattedChargeAmount() }}</dd>
                  </div>
                @endif
              </dl>

              @if($registration->isConverted())
                <p class="save-note d-block">
                  <i class="fa-solid fa-circle-info me-1"></i>
                  Your fee of {{ $registration->formattedAmount() }} is collected in Nepali Rupees at the
                  Nepal Rastra Bank rate of {{ $registration->fxRateLabel() }}. Your card will be
                  charged {{ $registration->formattedChargeAmount() }}.
                </p>
              @endif

              <p class="save-note mt-3 d-block">
                <i class="fa-solid fa-lock me-1"></i>
                Card details are captured directly by our payment provider and never reach this website.
              </p>
            </div>
          </div>
        </div>

        <!-- ============ PAYMENT ============ -->
        <div class="col-lg-7">
          <div class="form-card h-100">
            <div class="form-section">
              <p class="form-section-title"><i class="fa-solid fa-credit-card"></i> Payment Method</p>

              <div id="payment-alert" class="alert alert-danger" role="alert" hidden
                   style="border-left:4px solid var(--red);background:#fdecec;color:#842029;padding:1rem 1.25rem;border-radius:6px;"></div>

              @if($session)
                <div id="payment-loading" class="text-center py-4">
                  <i class="fa-solid fa-circle-notch fa-spin fa-2x" style="color:var(--red)"></i>
                  <p class="mt-2 mb-0">Loading secure payment…</p>
                </div>

                <!-- Unified Checkout mounts the payment-method buttons here… -->
                <div id="payment-buttons"></div>
                <!-- …and the card entry form here. -->
                <div id="payment-form" class="mt-3"></div>
              @else
                <p class="content-text">
                  Please
                  <a href="{{ route('registration.payment', $registration->payment_reference) }}"
                     style="color:var(--red);font-weight:600;">try again</a>,
                  or contact the organising committee quoting reference
                  <strong>{{ $registration->paymentCode() }}</strong>.
                </p>
              @endif
            </div>
          </div>
        </div>

      </div>

      <p class="table-note mt-3">
        <i class="fa-solid fa-circle-info"></i>
        Keep this page open until the payment finishes. Closing it early will not charge you, but you will
        need to start the payment again.
      </p>

    </div>

  </div>
</section>

@endsection


@push('scripts')
@if($session)
{{-- The library URL and its integrity hash come from the capture context and
     differ per transaction, so they are never hard-coded. --}}
<script src="{{ $session['client_library'] }}"
        @if($session['client_library_integrity']) integrity="{{ $session['client_library_integrity'] }}" @endif
        crossorigin="anonymous"></script>
<script>
(function () {
  var captureContext = @json($session['jwt']);
  var processUrl     = @json(route('registration.payment.process', $registration->payment_reference));
  var csrfToken      = @json(csrf_token());

  var loadingEl = document.getElementById('payment-loading');
  var alertEl   = document.getElementById('payment-alert');

  function showError(message) {
    alertEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i>' + message;
    alertEl.hidden = false;
    alertEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function clearError() {
    alertEl.hidden = true;
  }

  // Hand the transient token to the server, which authorises the payment for
  // the amount it calculated — the browser never states the amount.
  function authorize(transientToken) {
    return fetch(processUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ transient_token: transientToken })
    }).then(function (response) {
      return response.json().catch(function () {
        return { success: false, message: 'The server returned an unexpected response.' };
      });
    });
  }

  async function launchCheckout() {
    var client;
    var checkout;

    try {
      client = await VAS.UnifiedCheckout(captureContext);

      // Log every SDK error centrally, whichever integration raised it.
      client.on('error', function (err) {
        console.error('UnifiedCheckout', err.source, err.code, err.message);
      });

      // autoProcessing false: mount() resolves with a transient token, and this
      // application authorises the payment server-side.
      checkout = await client.createCheckout({ autoProcessing: false });

      checkout.on('ready', function () {
        clearError();
        if (loadingEl) loadingEl.remove();
      });

      var transientToken = await checkout.mount({
        paymentSelection: '#payment-buttons',
        paymentScreen: '#payment-form'
      });

      clearError();

      var result = await authorize(transientToken);

      if (result.success && result.redirect) {
        window.location.href = result.redirect;
        return;
      }

      showError(result.message || 'The payment could not be completed. Please try again.');
    } catch (error) {
      if (loadingEl) loadingEl.remove();

      if (error && error.name === 'UnifiedCheckoutError') {
        console.error('UnifiedCheckout', error.reason, error.message, error.details);
        showError(messageFor(error.reason));
      } else {
        console.error(error);
        showError('Something went wrong while loading the payment form. Please refresh the page and try again.');
      }
    } finally {
      if (checkout) checkout.destroy();
      if (client) client.destroy();
    }
  }

  // Turn the SDK's machine-readable reason into something a delegate can act on.
  function messageFor(reason) {
    switch (reason) {
      case 'CAPTURE_CONTEXT_EXPIRED':
        return 'This payment session has expired. Please refresh the page to start a new one.';
      case 'CAPTURE_CONTEXT_INVALID':
      case 'UNUSED_TARGET_ORIGINS':
        return 'The payment session could not be verified. Please refresh the page, or contact the organising committee.';
      case 'COMPLETE_TRANSACTION_CANCELLED':
        return 'The payment was cancelled. Refresh the page to try again.';
      case 'COMPLETE_AUTHENTICATION_CANCELED':
      case 'COMPLETE_AUTHENTICATION_FAILED':
        return 'Card authentication was not completed. Please refresh the page and try again.';
      case 'MOUNT_PAYMENT_UNAVAILABLE':
        return 'No payment method is available in this browser. Please try a different browser or device.';
      case 'MOUNT_TOKEN_TIMEOUT':
      case 'MOUNT_TOKEN_XHR_ERROR':
        return 'The connection to the payment provider was interrupted. Please check your connection and refresh the page.';
      default:
        return 'The payment could not be completed. Please refresh the page and try again.';
    }
  }

  launchCheckout();
})();
</script>
@endif
@endpush
