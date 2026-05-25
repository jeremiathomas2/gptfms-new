@component('mail::message')
# {{ $subject }}

{!! nl2br(e($body)) !!}

Thanks,  
{{ config('app.name') }}
@endcomponent

