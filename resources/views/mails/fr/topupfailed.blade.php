<x-mail::message>
# Hello {{ $name }},
 
Your wallet top up of ${{ $amount }}  failed.
Please try again later.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>