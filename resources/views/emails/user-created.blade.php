@component('mail::message')
# Olá, {{ $user->name }}!

Seu usuário foi criado com sucesso pelo administrador.

@component('mail::button', ['url' => $url])
Acessar sistema do clube
@endcomponent

E-mail: {{ $user->email }}<br>
Senha: use a senha fornecida pelo administrador para acessar o sistema.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
