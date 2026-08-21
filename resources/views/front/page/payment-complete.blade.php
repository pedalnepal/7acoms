@extends('front.common.layout')

@php
  $paid    = $registration->isPaid();
  $pending = $registration->payment_status === \App\Models\Registration::PAYMENT_PENDING;
@endphp

@section('content')

<!-- ============================================================
     PAGE BANNER
============================================================ -->
<section class="page-banner">
  <img class="page-banner-img" src="{{ asset('images/banner-1.jpg') }}" alt="Oral and Maxillofacial Surgery congress">
  <div class="page-banner-overlay"></div>
  <div class="container">
    <div class="page-banner-body">
      <h1 class="page-banner-title">
        @if($paid) Registration Confirmed
        @elseif($pending) Payment Received
        @else Payment Outstanding
        @endif
      </h1>
      <p class="page-banner-sub">7th ACOMS Trainee Conference 2027, Kathmandu</p>
    </div>
  </div>
</section>


<!-- ============================================================
     CONTENT
============================================================ -->
<section class="section-content">
  <div class="container">

    <div class="content-block">

      @if($paid || $pending)
        <div class="pay-result is-success">
          <i class="fa-solid fa-circle-check pay-result-icon"></i>
          <h2>Thank you, {{ $registration->full_name }}</h2>
          @if($paid)
            <p>
              Your payment of <strong>{{ $registration->formattedAmount() }}</strong> has been received and your
              place at the congress is confirmed. A receipt is on its way to
              <strong>{{ $registration->email }}</strong>.
            </p>
          @else
            <p>
              Your payment of <strong>{{ $registration->formattedAmount() }}</strong> has been accepted and is being
              confirmed by our payment provider. We will email <strong>{{ $registration->email }}</strong>
              as soon as it settles.
            </p>
          @endif
        </div>
      @else
        <div class="pay-result is-pending">
          <i class="fa-solid fa-circle-exclamation pay-result-icon"></i>
          <h2>Your payment is not yet complete</h2>
          <p>
            Your registration details are saved, but the fee has not been paid. You can pick up where you
            left off at any time.
          </p>
          <a href="{{ route('registration.payment', $registration->payment_reference) }}" class="btn-reg-lg mt-2">
            <i class="fa-solid fa-credit-card me-1"></i>Pay now
          </a>
        </div>
      @endif

      <div class="form-card mt-4">
        <div class="form-section">
          <p class="form-section-title"><i class="fa-solid fa-receipt"></i> Registration Details</p>

          <dl class="pay-summary">
            <div class="pay-row">
              <dt>Reference</dt>
              <dd><strong>{{ $registration->paymentCode() }}</strong></dd>
            </div>
            <div class="pay-row">
              <dt>Category</dt>
              <dd>{{ $registration->category ?? '—' }}</dd>
            </div>
            <div class="pay-row">
              <dt>Registering For</dt>
              <dd>{{ $registration->reg_for ?? '—' }}</dd>
            </div>
            <div class="pay-row">
              <dt>Amount</dt>
              <dd>{{ $registration->formattedAmount() }}</dd>
            </div>
            @if($registration->paid_at)
              <div class="pay-row">
                <dt>Paid on</dt>
                <dd>{{ $registration->paid_at->format('d M Y, H:i') }}</dd>
              </div>
            @endif
            @if($transaction && $transaction->card_masked)
              <div class="pay-row">
                <dt>Card</dt>
                <dd>{{ $transaction->card_masked }}</dd>
              </div>
            @endif
            @if($transaction && $transaction->transaction_id)
              <div class="pay-row">
                <dt>Transaction ID</dt>
                <dd>{{ $transaction->transaction_id }}</dd>
              </div>
            @endif
          </dl>

          <p class="table-note mt-3">
            <i class="fa-solid fa-circle-info"></i>
            Please quote the reference above in any correspondence with the organising committee.
          </p>
        </div>
      </div>

      <div class="mt-4">
        <a href="{{ url('/') }}" class="btn-reg-lg">
          <i class="fa-solid fa-house me-1"></i>Back to home
        </a>
      </div>

    </div>

  </div>
</section>

@endsection
