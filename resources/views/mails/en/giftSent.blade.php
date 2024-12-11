<x-mail::message>
# Hello {{ $name }},
 
Your gift of {{ $amount }} credits sent to {{ $recipient }} has been processed successfully.

{{ $recipient }} has received your gift and {{ $amount }} has been deducted from your Ndloo wallet.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>