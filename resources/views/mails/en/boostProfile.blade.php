<x-mail::message>
# Hello {{ $name }},

You boosted your profile for {{ $days }}days at the price of ${{ $price }}.

Your profile will now recieve more visibility among the matches of other users.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>