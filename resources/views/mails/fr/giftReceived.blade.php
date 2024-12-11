<x-mail::message>
# Hello {{ $name }},
 
Hurray!!!

You received ${{ $amount }} from {{ $sender }}. 

<br>
Love,

{{ config('app.name') }}
</x-mail::message>