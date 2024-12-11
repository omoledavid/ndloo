<x-mail::message>
# Hello {{ $name }},
 
Use {{ $token }} to reset your account password.

<small>This token was requested by {{ $email }} on account recovery.
If you did not make this request, kindly ignore this email
</small>

<br>
Love,
{{ config('app.name') }}
</x-mail::message>