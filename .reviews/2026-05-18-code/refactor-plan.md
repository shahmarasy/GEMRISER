# Refactor Plan — GEMRISER

**Tarih:** 2026-05-18
**Mode:** code → full rewrite
**Bağlam:** Legacy v0.5.3 → GEMRISER 2.0 (modern PHP 8.3 micro framework). 15 faz, ~9.25 gün.

## Faz 0 — Legacy Arşiv + Monorepo İskeleti (0.5g)

- **Why now:** Tüm diğer fazların ön koşulu. Legacy kod `legacy/v0.5.3` branch'ine arşivlenir, master monorepo yapısına geçer.
- **Related tasks:** Task 0
- **Exit kriteri:** `legacy/v0.5.3` branch'i remote'da; master'da eski dosyalar yok; monorepo dizin yapısı hazır.

## Faz 1 — Application + Container + Config (1g)

- **Why now:** Framework'ün kalbi — DI container, env yükleme, config sistemi. Sonraki tüm fazlar buna bağımlı.
- **Kapatılan legacy:** S10 (kısmen — `.env` altyapısı)
- **Related tasks:** Task 1
- **Exit kriteri:** `Application::bootstrap()` çalışır, `app('config')->get('app.name')` döner.

## Faz 2 — HTTP PSR-7/15 Pipeline (1g)

- **Why now:** Kernel + middleware stack. CSRF, Session, Error Handler, TrustProxies burada kurulur.
- **Kapatılan legacy:** S5 (LFI), S6 (CSRF), S9 (session), S12 (`$_SERVER` direkt)
- **Related tasks:** Task 2
- **Exit kriteri:** CSRF token'sız POST 419, session cookie Secure+HttpOnly+SameSite.

## Faz 3 — FastRoute Routing (0.5g)

- **Why now:** Router olmadan uygulama yazılamaz. Controller dispatch + DI injection.
- **Kapatılan legacy:** S5 (tamamen)
- **Related tasks:** Task 3
- **Exit kriteri:** Named route, route group, 404/405, `route('name')` URL üretme.

## Faz 4 — Eloquent ORM + Migrations (0.5g)

- **Why now:** Veri katmanı. `mysql_*` kullanımını bitirir, SQL injection'ı yapısal olarak kapatır.
- **Kapatılan legacy:** S1 (`mysql_*`), S2 (`create_function`), S3 (SQL injection)
- **Related tasks:** Task 4
- **Exit kriteri:** `User::create()`, `User::where(...)->first()` prepared statement üretir.

## Faz 5 — Blade Views (0.5g)

- **Why now:** Template engine. Auto-escaping ile XSS kapanır, `extract()` tarih olur.
- **Kapatılan legacy:** S4 (`extract`), S7 (XSS)
- **Related tasks:** Task 5
- **Exit kriteri:** `{{ '<b>x</b>' }}` → `&lt;b&gt;x&lt;/b&gt;`. `grep -rn 'extract(' packages/` boş.

## Faz 6 — Auth (bcrypt + Session) (0.5g)

- **Why now:** Kimlik doğrulama. bcrypt, session regenerate, throttle.
- **Kapatılan legacy:** S11 (`password_hash` yok)
- **Related tasks:** Task 6
- **Exit kriteri:** Login → session ID regenerate; 6 yanlış deneme → 429.

## Faz 7 — gemcli Console (0.5g)

- **Why now:** `artisan`-vari CLI. `make:*`, `migrate`, `route:list`, `key:generate`.
- **Related tasks:** Task 7
- **Exit kriteri:** `./gemcli list` tüm komutları gösterir; `make:controller` stub'tan dosya üretir.

## Faz 8 — Validation + Logging (0.25g)

- **Why now:** Form doğrulama + yapısal loglama.
- **Kapatılan legacy:** S8 (DB error ekrana dökülür)
- **Related tasks:** Task 8
- **Exit kriteri:** `validate(['email' => 'required'])` fail → 422 JSON / redirect. Exception → log + generic 500.

## Faz 9 — Skeleton Demo App (1g)

- **Why now:** Framework'ün çalıştığını kanıtlayan uygulama. Home + auth + CRUD + API.
- **Related tasks:** Task 9
- **Exit kriteri:** `./gemcli serve` → register/login/CRUD çalışır. `GET /api/examples` JSON döner.

## Faz 10 — Docker + Vite + Tailwind (0.75g)

- **Why now:** Geliştirme ortamı ve frontend pipeline.
- **Related tasks:** Task 10
- **Exit kriteri:** `docker compose up` ile 6 servis ayağa kalkar. `npm run build` manifest üretir.

## Faz 11 — Test + Lint + Static Analysis (1g)

- **Why now:** Kod kalitesi tooling'i. Pest 3 + PHPStan level 8 + Pint.
- **Related tasks:** Task 11
- **Exit kriteri:** `composer ci` (pint + phpstan + test) yeşil. Coverage ≥%70.

## Faz 12 — CI/CD + Subtree-Split (0.5g)

- **Why now:** GitHub Actions + Packagist yayını. Monorepo'dan ayrı repolara split.
- **Related tasks:** Task 12
- **Exit kriteri:** PR → CI yeşil. Tag push → subtree-split GitHub Release + Packagist.

## Faz 13 — v1.0.0 Release (0.25g)

- **Why now:** İlk kararlı sürüm.
- **Related tasks:** Task 13
- **Exit kriteri:** `composer create-project gemriser/skeleton test-app` çalışır.

## Faz 14 — Docs & Polish (0.5g)

- **Why now:** Community-ready belgeler. README, CONTRIBUTING, SECURITY.
- **Kapatılan legacy:** S10 (env hijyeni, final)
- **Related tasks:** Task 14
- **Exit kriteri:** Quick start 5 dk'da çalışır. `git ls-files | grep '\.env$'` boş.

---

## Legacy Bulgu → Faz Eşlemesi

| Bulgu | Açıklama | Faz |
|-------|----------|-----|
| S1 | `mysql_*` deprecated | 4 |
| S2 | `create_function()` | 4 |
| S3 | SQL injection (string concat) | 4 |
| S4 | `extract()` view'de | 5 |
| S5 | Sanitize edilmemiş `require()` (LFI) | 2+3 |
| S6 | CSRF yok | 2 |
| S7 | Output escaping yok | 5 |
| S8 | DB error ekrana dökülür | 8 |
| S9 | Session sertleştirme yok | 2 |
| S10 | DB credential repo'da | 1+14 |
| S11 | `password_hash` yok | 6 |
| S12 | `$_SERVER['REQUEST_URI']` direkt | 2 |

## Bağımlılık Haritası

```
Faz 0 (hazırlık)
  │
  ├──→ Faz 1 (Container)
  │     ├──→ Faz 2 (HTTP Pipeline) ──→ Faz 3 (Router)
  │     │                              │
  │     ├──→ Faz 4 (Eloquent) ─────────┤
  │     │                              │
  │     └──→ Faz 5 (Blade) ────────────┤
  │                                    │
  │                              ┌─────┘
  │                              ↓
  │                         Faz 6 (Auth)
  │                         Faz 7 (gemcli)
  │                         Faz 8 (Validation/Logging)
  │                              │
  │                              ↓
  │                         Faz 9 (Skeleton Demo)
  │                              │
  │                         Faz 10 (Docker + Frontend)
  │                              │
  │                         Faz 11 (Test/Lint/Static)
  │                              │
  │                         Faz 12 (CI/CD + Split)
  │                              │
  │                         Faz 13 (v1.0 Release)
  │                              │
  │                         Faz 14 (Docs)
```

## Risk Değerlendirmesi

| Risk | İhtimal | Etki | Mitigasyon |
|------|---------|------|------------|
| PHP 8.3 bağımlılıkları uyumsuz | D | Y | `composer require` öncesi `--with-all-dependencies` ile test |
| subtree-split PAT expiration | O | Y | `gh secret list` ile aylık kontrol; GitHub App alternatifi |
| Packagist name `gemriser/framework` dolu | D | O | Alternatif: `gemriser-php/framework` |
| Coverage hedefi (%70) ilk seferde tutmaz | O | O | İteratif hedef: başta %50, Faz 11'de %70 |
| Blade compiled path yazılabilir değil (prod) | O | Y | Deployment runbook'ta `chmod -R 775 storage/` uyarısı |
