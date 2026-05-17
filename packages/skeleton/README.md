# GEMRISER Skeleton

Starter application for GEMRISER framework.

## Quick Start

```bash
composer create-project gemriser/skeleton my-app
cd my-app
cp .env.example .env
./gemcli key:generate
./gemcli migrate --seed
./gemcli serve
```

## Commands

| Command | Description |
|---------|-------------|
| `./gemcli serve` | Start development server |
| `./gemcli migrate` | Run database migrations |
| `./gemcli migrate:rollback` | Rollback last migration |
| `./gemcli migrate:status` | Show migration status |
| `./gemcli route:list` | Display all routes |
| `./gemcli key:generate` | Generate APP_KEY |
| `./gemcli make:controller` | Create a controller |
| `./gemcli make:model` | Create a model |
| `./gemcli make:middleware` | Create a middleware |
