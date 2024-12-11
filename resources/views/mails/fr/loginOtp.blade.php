<x-mail::message>
# Hello {{ $name }},
 
Use {{ $token }} to authenticate your account.

<small>This token was requested by {{ $email }} to sign in.
If you did not make this request, kindly ignore this email
</small>

<br>
Love,

{{ config('app.name') }}
</x-mail::message>