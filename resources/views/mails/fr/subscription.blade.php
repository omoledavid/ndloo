<x-mail::message>
# Hello {{ $name }},
 
You have successfully subscribed to {{ $plan }} plan.

${{ $price }} has been deducted from your wallet. This amount will be deducted monthly.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>