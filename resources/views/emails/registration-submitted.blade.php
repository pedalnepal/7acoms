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
- **NAOMS Member:** {{ $registration->naoms_member ?? '—' }}@if($registration->member_id) (ID: {{ $registration->member_id }})@endif
- **Registering For:** {{ $registration->reg_for ?? '—' }}
- **Category:** {{ $registration->category ?? '—' }}
- **Accommodation:** {{ $registration->accommodation ?? '—' }}@if($registration->acc_rooms) — {{ $registration->acc_rooms }} room(s), {{ $registration->acc_type ?? 'n/a' }}@endif
- **Accompanying Person:** {{ $registration->accompanying ?? '—' }}@if($registration->acp_count) — {{ $registration->acp_count }} person(s)@endif
@endcomponent

@if($registration->others)
**Remarks:** {{ $registration->others }}
@endif

The uploaded **ID card** and **payment receipt** are attached to this email.

@component('mail::button', ['url' => url('/admin/dashboard/registration/' . $registration->id), 'color' => 'green'])
View in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
