@component('mail::message')
@include('emails.partials.logo')
# Thank you for registering

Dear {{ $registration->full_name }},

We have received your registration for the **7th ACOMS Trainee Conference 2027**, Kathmandu.

@component('mail::panel')
- **Reference:** {{ $registration->paymentCode() }}
- **Registering For:** {{ $registration->reg_for ?? '—' }}
- **Category:** {{ $registration->category ?? '—' }}
@if ($registration->amount)
- **Fee due:** {{ $registration->formattedAmount() }}
@endif
- **Status:** {{ $registration->isPaid() ? 'Confirmed' : 'Awaiting payment' }}
@endcomponent

@if (! $registration->isPaid() && $registration->payment_reference)
Your place is confirmed once the registration fee is paid. You can pay securely by card using the link
below — it stays valid, so you can come back to it at any time.

@component('mail::button', ['url' => route('registration.payment', $registration->payment_reference)])
Pay registration fee
@endcomponent

If you have already paid by bank transfer, you can ignore this — the organising committee will verify your
receipt and confirm your place.
@endif

If we need any further information, we will contact you at this email address.
If you did not make this registration, please ignore this email.

Warm regards,<br>
{{ config('app.name') }}
@endcomponent
