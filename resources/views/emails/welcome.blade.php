Hello {{$user->name}}
Thank you for create an account. Please vetify your email using link:
{{route('vetify', $user->verification_token)}}