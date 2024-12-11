<x-mail::message>
# Hello {{ $name }},
 
Your gift of {{ $amount }} credits sent to {{ $recipient }} could not be completed.

Please try again later. If you have been charged, kindly sent an email to <a href="mailto: support@ndloo.com">support@ndloo.com</a> and this will be rectified.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>