<x-mail::message>
# Hello {{ $name }},
 
Your gift of ${{ $amount }} sent to {{ $recipient }} has been processed successfully. 

<br>
Love,

{{ config('app.name') }}
</x-mail::message>