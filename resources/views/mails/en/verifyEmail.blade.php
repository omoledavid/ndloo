<x-mail::message>
# Hello {{ $name }},
 
Use {{ $token }} to verify your Ndloo account.

<small>This token was requested by {{ $email }} on signup.
If you did not make this request, kindly ignore this email
</small>

<br>
Love,

{{ config('app.name') }}
</x-mail::message>