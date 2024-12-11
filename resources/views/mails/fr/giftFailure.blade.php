<x-mail::message>
# Hello {{ $name }},
 
Your gift of ${{ $amount }} to {{ $recipient }} could not be completed.

Please try again later.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>