<x-mail::message>
# Hello {{ $name }},

You have successfully unsubscribed from {{ $plan }} plan.
You are now back on the Free plan.

<br>
Love,

{{ config('app.name') }}
</x-mail::message>