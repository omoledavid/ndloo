<x-mail::message>
# Hello {{ $name }},
 
You have successfully subscribed to {{ $plan }} plan.

${{ $price }} has been deducted from your wallet.

This subscription expires on {{ $expiry }} and will be autorenewed then.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>