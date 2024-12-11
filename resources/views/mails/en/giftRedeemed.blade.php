<x-mail::message>
# Hello {{ $name }},
 
You converted a gift of {{ $amount * 2 }} credits to cash.

${{ $amount }} has been deposited in your Ndloo wallet.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>