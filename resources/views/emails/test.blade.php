<x-mail::message>
# SMTP Test Successful

This is a test email from the **{{ config('app.name') }}** system. 
Your SMTP configuration with Gmail is working correctly.

<x-mail::button :url="config('app.url')">
Visit Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
