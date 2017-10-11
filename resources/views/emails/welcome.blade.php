@component('mail::message')
# Hello {{$user->name}}

Thank you for create an account. Please vetify your email using button:

@component('mail::button', ['url' => route('verify', $user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
