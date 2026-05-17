# Changelog

All notable changes to GEMRISER will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-05-18

### Added
- **Framework rewrite** from legacy v0.5.3 — Lumen-inspired micro PHP framework
- PSR-7/PSR-15 HTTP foundation (nyholm/psr7, relay/relay)
- FastRoute-based routing with DI parameter injection
- Eloquent ORM capsule (illuminate/database) with migrations
- Blade template engine (illuminate/view) with auto-escaping
- bcrypt-based session authentication with CSRF, throttling
- `gemcli` console (Symfony Console) with make:*, migrate:*, route:list
- Monolog logging with daily/single/stderr channels
- Illuminate/validation integration via `Request::validate()`
- Skeleton demo app: home + register/login/logout + dashboard + API

### Security
- All legacy v0.5.3 vulnerabilities resolved (S1–S12)
- No more `mysql_*`, prepared statements everywhere
- Output escaping (Blade `{{ }}`), CSRF tokens, session hardening
- bcrypt password hashing

### Infrastructure
- Monorepo with subtree-split (gemriser/framework, gemriser/skeleton)
- Docker Compose (php 8.3, nginx, mysql 8, redis 7, mailhog)
- GitHub Actions CI: Pint + PHPStan level 8 + Pest (coverage ≥70%)
- Vite + Tailwind + Alpine.js frontend pipeline

[1.0.0]: https://github.com/gemriser/gemriser/releases/tag/v1.0.0
