# Tasks — GEMRISER 2.0

**Tarih:** 2026-05-18
**Mode:** code → full rewrite

> Her task 15 fazdan birini temsil eder. Sıra bağımlılık sırasıdır.
> 🔴 = legacy güvenlik bulgusu kapatır, 🟡 = kalite/tooling

---

## Task 0: Legacy Arşiv + Monorepo İskeleti

- **Why:** Legacy v0.5.3 kodunu `legacy/v0.5.3` branch'ine arşivle; master'da monorepo iskeletini kur (packages/framework + packages/skeleton).
- **How:**
  1. `git switch -c legacy/v0.5.3`, push, `git switch master`
  2. `git rm -r application/ system/ static/ index.php .htaccess`
  3. Root `composer.json` (dev deps + replace), `monorepo-builder.php`, `.gitignore`, `.editorconfig`
  4. `packages/framework/composer.json` (PSR-4 `Gemriser\`)
  5. `packages/skeleton/composer.json` (PSR-4 `App\`)
- **Acceptance:** `git branch -a` → `legacy/v0.5.3` remote'da. `composer validate` root + paketler temiz. `tree -L 3 -I vendor` monorepo yapısını yansıtır.
- **File:** (yeni) `composer.json`, `monorepo-builder.php`, `.gitignore`, `.editorconfig`, `packages/*/composer.json`
- **Severity:** 🔴
- **Lens:** code
- **Depends on:** —
- **E2E scenario:** S0

## Task 1: Application + Container + Config

- **Why:** Framework'ün kalbi. DI container (`illuminate/container`), env yükleme (`vlucas/phpdotenv`), config repository. Tüm sonraki fazların ön koşulu.
- **How:**
  1. `illuminate/container`, `illuminate/contracts`, `vlucas/phpdotenv`, `psr/container` ekle
  2. `src/Container/Container.php` — PSR-11 proxy
  3. `src/Config/Repository.php` — dot-notation get/set
  4. `src/Config/Loader.php` — `config/*.php` dosyalarını yükle
  5. `src/Application.php` — Container extend, bootstrap (env → config → providers)
  6. `src/Providers/ServiceProvider.php` (abstract)
  7. `src/Support/helpers.php` — `app()`, `config()`, `env()`, `base_path()`
- **Acceptance:** `$app = new Application(__DIR__); $app->bootstrap(); echo $app->config->get('app.name');` çalışır. `vendor/bin/pest tests/Unit` yeşil.
- **File:** `packages/framework/src/Application.php`, `src/Container/*`, `src/Config/*`, `src/Providers/*`, `src/Support/*`
- **Severity:** 🔴
- **Lens:** code
- **Depends on:** Task 0
- **E2E scenario:** S1

## Task 2: HTTP PSR-7/15 Pipeline

- **Why:** Kernel + middleware stack. CSRF, Session, ErrorHandler, TrustProxies. Legacy S5, S6, S9, S12 kapanır.
- **How:**
  1. `nyholm/psr7`, `nyholm/psr7-server`, `relay/relay`, `filp/whoops`, `monolog/monolog` ekle
  2. `src/Http/Request.php` (PSR-7 decorator + input/validate helpers)
  3. `src/Http/Response.php` (json, html, redirect, view factory'leri)
  4. `src/Http/Kernel.php` (PSR-15 middleware queue)
  5. Middleware: `ErrorHandlerMiddleware`, `SessionMiddleware`, `EncryptCookiesMiddleware`, `CsrfMiddleware`, `ThrottleMiddleware`, `TrustProxiesMiddleware`
- **Acceptance:** CSRF token'sız POST → 419; doğru token → 200. Session cookie `Secure; HttpOnly; SameSite=Lax`. 61. istek → 429 + `Retry-After`.
- **File:** `packages/framework/src/Http/{Request,Response,Kernel,ResponseFactory}.php`, `src/Http/Middleware/*.php`
- **Severity:** 🔴
- **Lens:** security
- **Depends on:** Task 1
- **E2E scenario:** S1

## Task 3: FastRoute Routing

- **Why:** Route tanımlama, named routes, controller dispatch + DI parameter injection. Legacy S5 tamamen kapanır.
- **How:**
  1. `nikic/fast-route` ekle
  2. `src/Routing/{Route,RouteCollection,Router,UrlGenerator,RouteServiceProvider}.php`
  3. Kernel pipeline son handler'ı Router::dispatch
  4. Controller reflection ile DI parameter injection
- **Acceptance:** `GET /x` → 200. `route('users.show', ['id' => 5])` → `/users/5`. Unknown URI 404, yanlış method 405 + `Allow`.
- **File:** `packages/framework/src/Routing/*.php`, `src/Http/Kernel.php`
- **Severity:** 🔴
- **Lens:** code
- **Depends on:** Task 2
- **E2E scenario:** S1

## Task 4: Eloquent ORM + Migrations

- **Why:** Veri katmanı. `mysql_*` (S1), `create_function` (S2), SQL injection (S3) yapısal olarak kapanır.
- **How:**
  1. `illuminate/database`, `illuminate/events`, `illuminate/filesystem` ekle
  2. `src/Database/DatabaseServiceProvider.php` — Capsule Manager
  3. `src/Database/Migration/{Migrator,Migration}.php` — thin wrapper
  4. `src/Database/Seeder.php` — abstract base
- **Acceptance:** `app('db')->connection()->getPdo()` PDO döner. Test migration sqlite in-memory'de up/down çalışır. `User::where('email', 'a@b.c')` prepared statement üretir.
- **File:** `packages/framework/src/Database/*.php`, `src/Database/Migration/*.php`
- **Severity:** 🔴
- **Lens:** code / security
- **Depends on:** Task 1
- **E2E scenario:** S1

## Task 5: Blade Views

- **Why:** Template engine. `{{ }}` auto-escape ile XSS (S7) kapanır, `extract()` (S4) tarih olur.
- **How:**
  1. `jenssegers/blade` ekle
  2. `src/View/{BladeServiceProvider,ViewFactory,View}.php`
  3. Custom directive'ler: `@csrf`, `@method`, `@vite`, `@error`, `@auth`, `@guest`
  4. `Response::view()` factory
  5. `view()`, `csrf_token()`, `csrf_field()`, `method_field()`, `old()` helper'lar
- **Acceptance:** `{{ '<b>x</b>' }}` → `&lt;b&gt;x&lt;/b&gt;`. `@extends('layout') + @section` çalışır. `grep -rn 'extract(' packages/` boş.
- **File:** `packages/framework/src/View/*.php`, `src/Http/Response.php`, `src/Support/helpers.php`
- **Severity:** 🔴
- **Lens:** security / code
- **Depends on:** Task 1
- **E2E scenario:** S2 (XSS kontrolü)

## Task 6: Auth (bcrypt + Session)

- **Why:** Kimlik doğrulama. bcrypt hash (S11 kapanır), session regenerate, throttle.
- **How:**
  1. `illuminate/hashing`, `illuminate/encryption` ekle
  2. `src/Hashing/HashServiceProvider.php`, `src/Auth/{AuthManager,Guard,UserProvider,EloquentUserProvider,Authenticatable}.php`
  3. Middleware: `AuthenticateMiddleware`, `GuestMiddleware`, `ThrottleLoginMiddleware`
  4. `@auth`/`@guest` Blade directive'leri
- **Acceptance:** `Hash::make('secret')` → bcrypt. `auth()->attempt()` → login + session ID regenerate. 6 yanlış login → 429.
- **File:** `packages/framework/src/Auth/*.php`, `src/Hashing/*.php`, `src/Http/Middleware/{Authenticate,Guest,ThrottleLogin}*.php`
- **Severity:** 🔴
- **Lens:** security
- **Depends on:** Task 2, Task 4, Task 5
- **E2E scenario:** S1

## Task 7: gemcli Console

- **Why:** `artisan`-vari CLI. `make:*`, `migrate`, `route:list`, `key:generate`, `serve`.
- **How:**
  1. `symfony/console` ekle
  2. `src/Console/Application.php`, `src/Console/Command.php`
  3. Komutlar: `Serve`, `KeyGenerate`, `Migrate/Rollback/Fresh/Status/Refresh`, `DbSeed`, `Make{Controller,Model,Migration,Middleware,Factory,Seeder,Provider}`, `RouteList/Cache/Clear`, `ViewCache/Clear`, `ConfigCache/Clear`
  4. `stubs/` klasörü (controller, model, migration, middleware, factory, seeder, provider)
  5. Skeleton'da `gemcli` executable
- **Acceptance:** `./gemcli list` tüm komutlar. `./gemcli make:controller FooController` stub üretir. `./gemcli key:generate` `.env`'yi günceller.
- **File:** `packages/framework/src/Console/*.php`, `packages/skeleton/gemcli`
- **Severity:** 🟡
- **Lens:** code
- **Depends on:** Task 1, Task 4, Task 5
- **E2E scenario:** S1

## Task 8: Validation + Logging

- **Why:** Form doğrulama + yapısal loglama. Legacy S8 (DB error ekrana dökülür) kapanır.
- **How:**
  1. `illuminate/validation`, `illuminate/translation`, `monolog/monolog` ekle
  2. `src/Validation/ValidationServiceProvider.php` + `Request::validate()`
  3. `src/Exceptions/{ValidationException,Handler}.php`
  4. `src/Logging/LogManager.php` + `LoggingServiceProvider`
  5. ErrorHandlerMiddleware'i Whoops/Monolog ile entegre et
- **Acceptance:** `validate(['email' => 'required'])` fail → 422 JSON / redirect. Exception → log + generic 500 (debug=false) / Whoops (debug=true).
- **File:** `packages/framework/src/Validation/*.php`, `src/Exceptions/*.php`, `src/Logging/*.php`, `src/Providers/LoggingServiceProvider.php`
- **Severity:** 🔴
- **Lens:** qa
- **Depends on:** Task 1, Task 2
- **E2E scenario:** S1

## Task 9: Skeleton Demo App

- **Why:** Framework'ün çalıştığını kanıtlayan uygulama. Home + auth + CRUD + API.
- **How:**
  1. `bootstrap/app.php` — provider'ları register et
  2. `public/index.php` — front controller
  3. Config: `app.php`, `database.php`, `session.php`, `view.php`, `auth.php`, `logging.php`, `services.php`
  4. Models: `User`, `Example`; Migrations: users, password_resets, examples, sessions
  5. Factories + Seeders + Controllers + Routes (web + api)
  6. Blade views: layouts, auth, examples, errors
- **Acceptance:** `./gemcli serve` → register/login/CRUD çalışır. `GET /api/examples` JSON döner. CSRF'siz POST → 419.
- **File:** `packages/skeleton/**` (~50 dosya)
- **Severity:** 🟡
- **Lens:** code
- **Depends on:** Task 1–8
- **E2E scenario:** S1

## Task 10: Docker + Frontend Pipeline

- **Why:** Geliştirme ortamı (Docker Compose) ve frontend (Vite + Tailwind + Alpine).
- **How:**
  1. `docker-compose.yml` (app, nginx, mysql, redis, mailhog, node)
  2. `docker/php/Dockerfile`, `docker/nginx/default.conf`, `.dockerignore`
  3. `package.json`, `vite.config.js`, `tailwind.config.js`, `postcss.config.js`
  4. `resources/scss/app.scss` + partial'lar; `resources/js/app.js` + bootstrap
  5. Vite manifest reader (`@vite` directive)
- **Acceptance:** `docker compose up` → 6 servis ayağa. `npm run build` manifest + hash'lenmiş assets. `http://localhost:8000` → Tailwind + Alpine çalışır.
- **File:** `docker-compose.yml`, `docker/*`, `packages/skeleton/{package.json,vite.config.js,tailwind.config.js,postcss.config.js,resources/**}`
- **Severity:** 🟡
- **Lens:** gtm
- **Depends on:** Task 9
- **E2E scenario:** S3

## Task 11: Test + Lint + Static Analysis

- **Why:** Kod kalitesi. Pest 3 + PHPStan level 8 + Pint + ESLint + Stylelint + commitlint.
- **How:**
  1. `pestphp/pest`, `pestphp/pest-plugin-arch`, `mockery/mockery`, `fakerphp/faker` ekle
  2. `phpunit.xml` her pakette, `Pest.php`, arch test'leri
  3. `phpstan.neon` (level 8), `pint.json`, `eslint.config.js`, `stylelint.config.js`, `.prettierrc`
  4. `commitlint.config.js`, `.husky/` (commit-msg + pre-commit)
  5. Root composer scripts: `test`, `test:coverage`, `phpstan`, `pint`, `ci`
- **Acceptance:** `composer ci` yeşil. Coverage ≥%70. PHPStan level 8 hatasız. `git commit -m "bad"` reddedilir.
- **File:** `phpstan.neon`, `pint.json`, `eslint.config.js`, `stylelint.config.js`, `.prettierrc`, `commitlint.config.js`, `.husky/*`, `composer.json` (scripts)
- **Severity:** 🟡
- **Lens:** qa
- **Depends on:** Task 1–10
- **E2E scenario:** S3

## Task 12: CI/CD + Subtree-Split

- **Why:** GitHub Actions CI + monorepo subtree-split + Packagist yayını.
- **How:**
  1. `.github/workflows/ci.yml` — lint → phpstan → test → coverage → scc report
  2. `.github/workflows/release.yml` — subtree-split (symplify) + GitHub Release
  3. `.github/dependabot.yml` — haftalık güncelleme
  4. Branch protection: PR + CI green + 1 review
- **Acceptance:** PR → CI yeşil. Tag push → subtree-split → Packagist'te yeni sürüm. Dependabot PR haftalık açılır.
- **File:** `.github/workflows/{ci,release}.yml`, `.github/dependabot.yml`
- **Severity:** 🟡
- **Lens:** gtm
- **Depends on:** Task 11
- **E2E scenario:** S3

## Task 13: v1.0.0 Release

- **Why:** İlk kararlı sürüm. Packagist + GitHub Release.
- **How:**
  1. CHANGELOG.md (Keep a Changelog formatı)
  2. Tag `v1.0.0` → push → release workflow tetiklenir
  3. Smoke test: `composer create-project gemriser/skeleton test-app`
- **Acceptance:** `composer create-project gemriser/skeleton /tmp/x` çalışan demo app üretir. Packagist'te `gemriser/framework` v1.0.0 görünür.
- **File:** `CHANGELOG.md`, git tag `v1.0.0`
- **Severity:** 🟡
- **Lens:** gtm
- **Depends on:** Task 12
- **E2E scenario:** S1, S3

## Task 14: Docs & Polish

- **Why:** Community-ready. README, CONTRIBUTING, SECURITY. Legacy S10 (env hijyeni) kesin kapanır.
- **How:**
  1. Root README.md — hero, quick start, arch diagram, stack, "Coming from Lumen?"
  2. `packages/framework/README.md`, `packages/skeleton/README.md`
  3. `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`
  4. `.github/ISSUE_TEMPLATE/{bug_report,feature_request}.md`, `PULL_REQUEST_TEMPLATE.md`
  5. Repo description + topics + website ayarları
- **Acceptance:** Quick start 5 dk'da çalışır. `git ls-files | grep -E '\.env$'` boş. Tüm badge'ler aktif.
- **File:** `README.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`, `SECURITY.md`, `.github/*TEMPLATE*`
- **Severity:** 🟡
- **Lens:** gtm
- **Depends on:** Task 13
- **E2E scenario:** S3
