# Insurance Platform


> **Languages / Idiomas / Idiomas:** [English](#-english) · [Español](#-español) · [Português](#-português)

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

### Delivery phases

This repository was built in progressive phases (see commit history):

| Phase | Focus |
|-------|--------|
| 1. Scaffold | Bootstrap Symfony project and ignore rules |
| 2. Domain | Entities, enums and repository ports (pure PHP) |
| 3. Application | Command handlers / use cases |
| 4. Infrastructure | Doctrine models and repository adapters |
| 5. API & security | HTTP controllers, JWT and config |
| 6. Database | Migrations and seed data |
| 7. Messaging | RabbitMQ publishers and workers (when applicable) |
| 8. Docker | Local Docker Compose stack |
| 9. Frontend tooling | Vue 3 + TypeScript + Vite scaffold |
| 10. Frontend UI | Application pages and API client |
| 11. Tests | Domain unit tests |
| 12. Docs & ops | Multi-language README, CI, Swarm/monitoring (when applicable) |

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

### Fases de entrega

Este repositorio se construyó en fases progresivas (ver historial de commits):

| Fase | Enfoque |
|-------|--------|
| 1. Scaffold | Bootstrap del proyecto Symfony e ignore rules |
| 2. Dominio | Entidades, enums e interfaces de repositorio (PHP puro) |
| 3. Aplicación | Command handlers / casos de uso |
| 4. Infraestructura | Models Doctrine y adapters de repositorio |
| 5. API y seguridad | Controllers HTTP, JWT y configuración |
| 6. Base de datos | Migraciones y datos seed |
| 7. Mensajería | Publishers y workers RabbitMQ (cuando aplique) |
| 8. Docker | Stack Docker Compose local |
| 9. Frontend tooling | Scaffold Vue 3 + TypeScript + Vite |
| 10. Frontend UI | Páginas de la aplicación y cliente API |
| 11. Tests | Tests unitarios de dominio |
| 12. Docs & ops | README multi-idioma, CI, Swarm/monitoring (cuando aplique) |

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

### Fases de entrega

Este repositório foi construído em fases progressivas (ver histórico de commits):

| Fase | Foco |
|-------|--------|
| 1. Scaffold | Bootstrap do projeto Symfony e ignore rules |
| 2. Domínio | Entidades, enums e portas de repositório (PHP puro) |
| 3. Aplicação | Command handlers / casos de uso |
| 4. Infraestrutura | Models Doctrine e adapters de repositório |
| 5. API e segurança | Controllers HTTP, JWT e configuração |
| 6. Banco de dados | Migrations e dados seed |
| 7. Mensageria | Publishers e workers RabbitMQ (quando aplicável) |
| 8. Docker | Stack Docker Compose local |
| 9. Frontend tooling | Scaffold Vue 3 + TypeScript + Vite |
| 10. Frontend UI | Páginas da aplicação e cliente da API |
| 11. Testes | Testes unitários de domínio |
| 12. Docs & ops | README multi-idioma, CI, Swarm/monitoring (quando aplicável) |

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
