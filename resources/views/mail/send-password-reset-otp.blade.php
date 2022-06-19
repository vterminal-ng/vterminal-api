@component('mail::message')
# Hi there,

Use the verification code below to reset your password.

# {{$otp}}.

Don’t recognize this activity? Please change your password and contact customer support immediately. 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
