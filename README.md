# Desafio 8 — Insurance Platform

---

## 🇬🇧 English

Insurance policy and claims management platform built with hexagonal architecture, DDD and SOLID principles.

### Stack

- **Backend**: PHP 8.2 + Symfony 7 + Doctrine ORM
- **Frontend**: Vue 3 + TypeScript + Vite + TailwindCSS
- **Database**: MySQL 8
- **Cache**: Redis 7
- **Messaging**: RabbitMQ 3
- **Auth**: JWT (lexik/jwt-authentication-bundle)
- **Infra**: Docker + docker-compose

### Architecture

```
src/
├── Domain/              # Pure business rules
│   ├── Policy/          # Insurance policies
│   ├── Claim/           # Claims
│   └── Shared/          # Shared value objects
├── Application/         # Use cases (Command Handlers)
└── Infrastructure/      # Adapters (Doctrine, HTTP, RabbitMQ)
```

### How to run

```bash
docker compose up -d
```

- Frontend: http://localhost:3008
- API: http://localhost:8140/api/v1
- RabbitMQ Management: http://localhost:15674 (guest/guest)

**Login**: admin@insurance.test / password

### Domain flows

**Policy**: `draft` → `active` → `suspended` → `cancelled` / `expired`

**Claim**: `reported` → `under_review` → `approved` → `paid` / `rejected`

### Tests

```bash
docker compose exec app ./vendor/bin/phpunit
```

---

## 🇪🇸 Español

Plataforma de gestión de pólizas de seguro y siniestros construida con arquitectura hexagonal, DDD y principios SOLID.

### Stack

- **Backend**: PHP 8.2 + Symfony 7 + Doctrine ORM
- **Frontend**: Vue 3 + TypeScript + Vite + TailwindCSS
- **Base de datos**: MySQL 8
- **Caché**: Redis 7
- **Mensajería**: RabbitMQ 3
- **Auth**: JWT (lexik/jwt-authentication-bundle)
- **Infra**: Docker + docker-compose

### Arquitectura

```
src/
├── Domain/              # Reglas de negocio puras
│   ├── Policy/          # Pólizas de seguro
│   ├── Claim/           # Siniestros
│   └── Shared/          # Value Objects compartidos
├── Application/         # Casos de uso (Command Handlers)
└── Infrastructure/      # Adaptadores (Doctrine, HTTP, RabbitMQ)
```

### Cómo ejecutar

```bash
docker compose up -d
```

- Frontend: http://localhost:3008
- API: http://localhost:8140/api/v1
- RabbitMQ Management: http://localhost:15674 (guest/guest)

**Login**: admin@insurance.test / password

### Flujos de dominio

**Póliza**: `draft` → `active` → `suspended` → `cancelled` / `expired`

**Siniestro**: `reported` → `under_review` → `approved` → `paid` / `rejected`

### Tests

```bash
docker compose exec app ./vendor/bin/phpunit
```

---

## 🇧🇷 Português

Plataforma de gestão de apólices de seguro e sinistros construída com arquitetura hexagonal, DDD e princípios SOLID.

### Stack

- **Backend**: PHP 8.2 + Symfony 7 + Doctrine ORM
- **Frontend**: Vue 3 + TypeScript + Vite + TailwindCSS
- **Banco de dados**: MySQL 8
- **Cache**: Redis 7
- **Mensageria**: RabbitMQ 3
- **Auth**: JWT (lexik/jwt-authentication-bundle)
- **Infra**: Docker + docker-compose

### Arquitetura

```
src/
├── Domain/              # Regras de negócio puras
│   ├── Policy/          # Apólices de seguro
│   ├── Claim/           # Sinistros
│   └── Shared/          # Value Objects compartilhados
├── Application/         # Casos de uso (Command Handlers)
└── Infrastructure/      # Adaptadores (Doctrine, HTTP, RabbitMQ)
```

### Como executar

```bash
docker compose up -d
```

- Frontend: http://localhost:3008
- API: http://localhost:8140/api/v1
- RabbitMQ Management: http://localhost:15674 (guest/guest)

**Login**: admin@insurance.test / password

### Fluxos de domínio

**Apólice**: `draft` → `active` → `suspended` → `cancelled` / `expired`

**Sinistro**: `reported` → `under_review` → `approved` → `paid` / `rejected`

### Testes

```bash
docker compose exec app ./vendor/bin/phpunit
```
