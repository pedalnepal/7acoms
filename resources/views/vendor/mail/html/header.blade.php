@props(['url'])
<tr>
<td class="header" style="background-color:#005F75">
<a href="{{ route('home')}}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
    <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
@else
    <span style="color:#ffffff;font-size:18px;font-weight:600;">{{ $slot }}</span>
@endif
</a>
</td>
</tr>
