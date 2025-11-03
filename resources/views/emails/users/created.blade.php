<x-mail::message>
# You are invited to {{ config('app.name') }}

Your temporary password is {{ $password }}

<x-mail::button :url="route('login')">
Login
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
