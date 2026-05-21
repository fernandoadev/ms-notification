# ms-notification

Microserviço de comunicação centralizada responsável por receber, enfileirar e processar envios por diferentes canais (e-mail, SMS e push notification) de forma assíncrona com rastreabilidade completa via logs.

## Stack

- PHP 8.4 + Laravel 12
- MySQL 8.0
- Laravel Queue (driver `database`)
- Docker + Docker Compose

---

## Pré-requisitos

- [Docker](https://www.docker.com/) e Docker Compose instalados
- [Composer](https://getcomposer.org/) instalado localmente

---

## Instalação e execução

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd ms-notification
```

### 2. Copie o arquivo de ambiente

```bash
cp .env.example .env
```

### 3. Instale as dependências PHP

```bash
composer install
```

### 4. Suba os containers

```bash
docker compose up -d
```

Isso irá subir três serviços:

| Serviço | Endereço |
|---|---|
| Aplicação (Laravel) | http://localhost:8000 |
| MySQL | localhost:3306 |
| phpMyAdmin | http://localhost:8080 |

### 5. Gere a chave da aplicação

```bash
docker compose exec ms-notification php artisan key:generate
```

### 6. Execute as migrations

```bash
docker compose exec ms-notification php artisan migrate
```

### 7. Suba o worker da fila

Em um terminal separado, mantenha o worker em execução para processar os jobs de comunicação:

```bash
docker compose exec ms-notification php artisan queue:work --queue=communications
```

---

## Uso da API

### Criar uma comunicação

**`POST /api/communications`**

```bash
curl -X POST http://localhost:8000/api/communications \
  -H "Content-Type: application/json" \
  -d '{
    "recipient": "usuario@email.com",
    "channel": "email",
    "subject": "Bem-vindo",
    "message": "Sua conta foi criada com sucesso.",
    "origin_system": "sistema-financeiro"
  }'
```

**Canais disponíveis:** `email`, `sms`, `push`

**Resposta de sucesso (`201`):**

```json
{
  "message": "Communication request received and is being processed.",
  "communication": { ... }
}
```

---

## Testes

Os testes utilizam SQLite em memória e não dependem dos containers para rodar.

```bash
docker compose exec ms-notification php artisan test
```

Para rodar uma suite específica:

```bash
# Unitários
docker compose exec ms-notification php artisan test tests/Unit --testdox

# Feature (integração)
docker compose exec ms-notification php artisan test tests/Feature --testdox
```

---

## Arquitetura

```
app/
├── Clients/          # Integrações HTTP com provedores externos (SMS, Email, Push)
├── DataTransferObjects/  # DTOs tipados para transporte entre camadas
├── Enums/            # Constantes de domínio (canal, status, nível de log)
├── Helpers/          # Utilitários estáticos (LogHelper)
├── Http/
│   ├── Controllers/  # Recebe a request, delega ao service, retorna response
│   └── Requests/     # Validação de entrada via Form Request
├── Jobs/             # ProcessCommunicationJob — processamento assíncrono
├── Models/           # Communication, CommunicationLog
└── Services/         # Regras de negócio (CommunicationService, CommunicationProcessService)
```