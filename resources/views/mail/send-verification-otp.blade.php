@component('mail::message')
# Hi {{ Auth::user()->fullname }}

Welcome to {{ config('app.name') }}. Verify your email by using the verification code below. 

Verification code

# {{$otp}}.

Don’t recognize this activity? Please reset your password and contact customer support immediately. 

Thanks,<br>
{{ config('app.name') }}
@endcomponent
