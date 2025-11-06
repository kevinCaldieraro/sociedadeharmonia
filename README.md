# 🏛️ Sociedade Harmonia

Sistema institucional da **Sociedade Harmonia**, desenvolvido em **Laravel**, **Vue 3** e **Vite**, com deploy automatizado em ambiente Linux (Nginx) e HTTPS configurado via **Certbot (Let's Encrypt)**.

---

## 🚀 Tecnologias Utilizadas

- **Backend:** [Laravel 11](https://laravel.com/)
- **Frontend:** [Vue 3](https://vuejs.org/) + [Vite](https://vitejs.dev/)
- **Empacotamento:** [Inertia.js](https://inertiajs.com/)
- **Servidor:** Nginx (Ubuntu)
- **Banco de Dados:** Postgres
- **Certificado SSL:** Let's Encrypt (Certbot)
- **Gerenciador de pacotes:** npm + Composer

---

## ⚙️ Estrutura do Projeto

```bash
├── app
├── artisan
├── bootstrap
├── components.d.ts
├── composer.json
├── composer.lock
├── config
├── database
├── docker
├── docker-compose.local.yml
├── eslint.config.js
├── LICENSE
├── package-lock.json
├── package.json
├── phpstan.neon
├── phpunit.xml
├── pint.json
├── public
├── README.md
├── resources
├── routes
├── tests
├── tsconfig.json
└── vite.config.js
```

---

## 🧑‍💻 Como Desenvolver Localmente

### 1. Pré-requisitos

Certifique-se de ter instalado:

- [PHP >= 8.2](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js >= 18](https://nodejs.org/)
- [npm](https://www.npmjs.com/)
- [PostgreSQL](https://www.postgresql.org/)

---

### 2. Clonar o repositório

```bash
$ git clone git@github.com:kevinCaldieraro/sociedadeharmonia.git
$ cd sociedadeharmonia
```

---

### 3. Instalar dependências

```bash
$ composer install
$ npm install
```

---

### 4. Configurar variáveis de ambiente

Copie o arquivo de exemplo:

```bash
$ cp .env.example .env
```

Edite o .env com suas configurações locais:

```.env
APP_NAME="Associação Harmonia Ivoti"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=America/Sao_Paulo
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sociedadeharmonia
DB_USERNAME=postgres
DB_PASSWORD=

# caso queira testar envios de emails, preencher conforme preferência de mailer (opcional):
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Em seguida, gere a chave da aplicação:

```bash
$ php artisan key:generate
```

---

### 5. Criar o banco de dados

```sql
CREATE DATABASE sociedadeharmonia;
```

Depois, rodar migrations:

```bash
$ php artisan migrate
```

Criar primeiro usuário:

```php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

User::create([
    'name' => 'Nome',
    'email' => 'email@gmail.com',
    'password' => Hash::make('senha123'),
    'is_admin' => 't',
]);
```

---

### 6. Executar o servidor local

```bash
$ composer run dev
```

O sistema estará disponível em:

```bash
http://localhost:8000
```

---

## 🧱 Build de Produção

Antes de realizar o deploy no servidor:

```bash
$ npm run build
```

Isso gerará os arquivos otimizados em public/build para serem usados em uma VPS, por exemplo.

---

## 🏭 Sistema em produção

> 🔗 [Associação Harmonia Ivoti](https://associacaoharmoniaivoti.cloud)