@component('mail::message')
@include('emails.partials.logo')
@if ($forAdmin)
# Registration payment received

A registration fee has been paid online.

@component('mail::panel')
- **Delegate:** {{ $registration->full_name }}
- **Email:** {{ $registration->email ?? '—' }}
- **Category:** {{ $registration->category ?? '—' }}
- **Amount:** {{ $registration->formattedAmount() }}
@if ($registration->isConverted())
- **Charged:** {{ $registration->formattedChargeAmount() }} ({{ $registration->fxRateLabel() }})
@endif
- **Reference:** {{ $transaction->reference }}
- **Transaction ID:** {{ $transaction->transaction_id ?? '—' }}
- **Status:** {{ $transaction->status }}
@endcomponent

@component('mail::button', ['url' => route('registration.admin.show', $registration->id)])
View registration
@endcomponent
@else
# Payment received

Dear {{ $registration->full_name }},

Thank you — we have received your payment for the **7th ACOMS Trainee Conference 2027**, Kathmandu.

@component('mail::panel')
- **Reference:** {{ $transaction->reference }}
- **Amount paid:** {{ $registration->formattedAmount() }}
@if ($registration->isConverted())
- **Charged to your card:** {{ $registration->formattedChargeAmount() }}
- **Exchange rate:** {{ $registration->fxRateLabel() }}
@endif
- **Paid on:** {{ optional($registration->paid_at)->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}
- **Category:** {{ $registration->category ?? '—' }}
- **Registering For:** {{ $registration->reg_for ?? '—' }}
@if ($transaction->card_masked)
- **Card:** {{ $transaction->card_masked }}
@endif
@endcomponent

@if ($registration->payment_status === \App\Models\Registration::PAYMENT_PENDING)
Your payment has been accepted and is being confirmed by our payment provider. We will be in touch once it settles.
@else
Your registration is now confirmed. Please keep this email — the reference above identifies your registration at the congress desk.
@endif

If you have any questions, simply reply to this email and the organising committee will help.

Warm regards,<br>
{{ config('app.name') }}
@endif
@endcomponent
