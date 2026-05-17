# Current State — GEMRISER

**Tarih:** 2026-05-18
**Mode:** code → full rewrite plan
**Reviewer:** Claude (product-review skill)

## Legacy (v0.5.3 — deprecated)

- **Backend:** PHP 5.x (uses deprecated `mysql_*` extension), custom mini MVC framework
- **Frontend:** Pure CSS 0.6.0 (vanilla HTML, no JS framework)
- **DB:** MySQL (via `mysql_connect` — removed in PHP 7+)
- **Cache/Queue:** Yok
- **LOC (yaklaşık):** ~550 PHP
- **Test, CI/CD, Docker:** Yok
- **README:** "GEMRISER (Deprecated) :("

## Target (GEMRISER 2.0)

| Katman | Stack |
|--------|-------|
| **PHP** | 8.3 — strict types, PSR-7/15 |
| **DI** | `illuminate/container` |
| **HTTP** | nyholm/psr7 + relay/relay (PSR-15 pipeline) |
| **Routing** | FastRoute + named routes + DI parameter injection |
| **DB** | Eloquent ORM capsule (`illuminate/database`) |
| **Views** | Blade (`jenssegers/blade`) — auto-escaping |
| **Auth** | bcrypt + session guard + CSRF + throttling |
| **CLI** | Symfony Console (`gemcli`) |
| **Logging** | Monolog |
| **Validation** | `illuminate/validation` |
| **Frontend** | Vite + Tailwind + SCSS + Alpine.js |
| **Container** | Docker Compose (php-fpm, nginx, mysql 8, redis 7, mailhog, node) |
| **CI/CD** | GitHub Actions (Pest, PHPStan 8, Pint, ESLint, subtree-split) |
| **Monorepo** | `packages/framework` + `packages/skeleton` → Packagist |
| **Skeleton** | Full demo app: auth + CRUD + API + Blade views |

## Topology (target)

```
gemriser/
├── packages/
│   ├── framework/          → gemriser/framework (Packagist)
│   │   ├── src/            (Application, Container, Http, Routing, Database, View, Auth, Console, Validation, Logging)
│   │   └── tests/
│   └── skeleton/           → gemriser/skeleton (Packagist)
│       ├── app/            (Controllers, Models, Providers)
│       ├── config/         (app, database, session, auth, view, logging, services)
│       ├── database/       (migrations, factories, seeders)
│       ├── resources/      (views, scss, js)
│       ├── routes/         (web.php, api.php)
│       ├── tests/
│       ├── gemcli          (entry point)
│       └── public/index.php
├── docker/ (php, nginx)
├── .github/workflows/
└── composer.json (root monorepo)
```

## Scope estimate

**Tahmin:** Feature-complete startup seviyesi (yeniden yazım sonrası)

**Kanıt:**
- Mevcut kod deprecated, PHP 7+ çalışmaz
- Worktree'de 15 fazlı, ~9.25 günlük kapsamlı rewrite planı hazır
- Hedef: Lumen'in modern halefi — tam yığın PHP mikro framework
