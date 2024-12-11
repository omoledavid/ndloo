<x-mail::message>
# Hello {{ $name }},
 
Your wallet top up of ${{ $amount }} was successful.
${{ $amount  }} has been added to your wallet.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>