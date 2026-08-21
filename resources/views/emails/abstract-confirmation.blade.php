@component('mail::message')
@include('emails.partials.logo')
# Thank you for your submission

Dear {{ $abstract->presenting_author ?: 'Author' }},

We have received your abstract for the **7th ACOMS Trainee Conference 2027**, Kathmandu. It is now under review by the scientific committee.

@component('mail::panel')
- **Title:** {{ $abstract->title }}
- **Topic Category:** {{ $abstract->category ?? '—' }}
- **Presentation Type:** {{ $abstract->pres_type ? ucfirst($abstract->pres_type) : '—' }}
- **Status:** Under review
@endcomponent

You will be notified of the committee's decision at this email address. If we need any further information, we will get in touch.

If you did not make this submission, please ignore this email.

Warm regards,<br>
{{ config('app.name') }}
@endcomponent
