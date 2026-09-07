@component('mail::message')
@include('emails.partials.logo')
# New Registration — {{ config('app.name') }}

A new delegate registration has been submitted through the website.

@component('mail::panel')
- **Name:** {{ $registration->full_name }}
- **Email:** {{ $registration->email ?? '—' }}
- **Phone:** {{ $registration->phone ?? '—' }}
- **Designation:** {{ $registration->designation ?? '—' }}
- **Working Place:** {{ $registration->workplace ?? '—' }}
- **Nationality:** {{ $registration->nationality ?? '—' }}
- **NAOMS Member:** {{ $registration->naoms_member ?? '—' }}
- **Registering For:** {{ $registration->reg_for ?? '—' }}
- **Category:** {{ $registration->category ?? '—' }}
@endcomponent

@if($registration->others)
**Remarks:** {{ $registration->others }}
@endif

The uploaded **recommendation letter** and **payment receipt** are attached to this email.

@component('mail::button', ['url' => url('/admin/dashboard/registration/' . $registration->id), 'color' => 'green'])
View in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
