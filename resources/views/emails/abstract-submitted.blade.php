@component('mail::message')
@include('emails.partials.logo')
# New Abstract Submission — {{ config('app.name') }}

A new abstract has been submitted through the website.

@component('mail::panel')
- **Title:** {{ $abstract->title }}
- **Authors:** {{ $abstract->authors ?? '—' }}
- **Affiliation:** {{ $abstract->affiliation ?? '—' }}
- **Presenting Author:** {{ $abstract->presenting_author ?? '—' }}
- **Designation:** {{ $abstract->designation ? ucfirst($abstract->designation) : '—' }}
- **Topic Category:** {{ $abstract->category ?? '—' }}
- **Presentation Type:** {{ $abstract->pres_type ? ucfirst($abstract->pres_type) : '—' }}
- **Research Type:** {{ $abstract->research_type ?? '—' }}
- **Presentation Category:** {{ $abstract->pres_category ? ucfirst($abstract->pres_category) : '—' }}
@endcomponent

**Abstract Body:**

{{ $abstract->abstract_body }}

@if($abstract->reference_list)
**References:**

{{ $abstract->reference_list }}
@endif

@if($abstract->file_path)
The uploaded **presentation file** is attached to this email.
@else
_No presentation file was uploaded._
@endif

@component('mail::button', ['url' => url('/admin/dashboard/abstract/' . $abstract->id), 'color' => 'green'])
View in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
