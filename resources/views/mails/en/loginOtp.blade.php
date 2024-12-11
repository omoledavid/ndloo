<x-mail::message>
# Hello {{ $name }},
 
Use {{ $token }} to authenticate your account.

<small>This token is a one time password and was requested by {{ $email }} to sign in to their account.

If you did not make this request, kindly ignore this email
</small>

<br>
Love,

{{ config('app.name') }}
</x-mail::message>