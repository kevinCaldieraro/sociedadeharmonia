@component('mail::message')
# Olá!

Recebemos uma solicitação para redefinir a senha da sua conta.

@component('mail::button', ['url' => $url])
Redefinir Senha
@endcomponent

Este link de redefinição de senha expira em {{ $expires }} minutos.

Se você não solicitou a redefinição de senha, nenhuma ação é necessária.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
