# Findings — GEMRISER

**Tarih:** 2026-05-18
**Mode:** code → full rewrite plan

> Bu proje **baştan yazılacak** (GEMRISER 2.0). Aşağıdaki bulgular legacy v0.5.3'e aittir; hepsi plan kapsamında kapatılacaktır.

## Genel Tablo

### Karar: Baştan yazım (rewrite)

**Gerekçe:** Legacy kod PHP 5.3 standartlarında, `mysql_*` PHP 7+ uyumsuz, 0 test, 0 güvenlik. Yama yaparak taşınamayacak kadar eski. Worktree'deki 15 fazlı plan (`quizzical-hermann`) modern PHP 8.3 stack ile sıfırdan inşa öngörüyor.

### Legacy Bulgular (rewrite ile kapanacak)

| ID | Bulgu | Severity | Kapatan Faz |
|----|-------|----------|-------------|
| S1 | `mysql_*` deprecated — PHP 7+ çalışmaz | 🔴 | Faz 4 (Eloquent) |
| S2 | `create_function()` kullanımı | 🔴 | Faz 4 |
| S3 | SQL injection (string concat query) | 🔴 | Faz 4 (prepared stmts) |
| S4 | `extract()` view'de — değişken çakışması | 🔴 | Faz 5 (Blade) |
| S5 | Sanitize edilmemiş `require()` (LFI riski) | 🔴 | Faz 2+3 (PSR-7 + Router) |
| S6 | CSRF koruması yok | 🔴 | Faz 2 (CsrfMiddleware) |
| S7 | Output escaping yok (XSS) | 🔴 | Faz 5 (Blade `{{ }}` auto-escape) |
| S8 | DB hata mesajı ekrana dökülür (`or die`) | 🔴 | Faz 8 (Monolog + Exception Handler) |
| S9 | Session sertleştirme yok (HttpOnly, Secure) | 🟡 | Faz 2 (SessionMiddleware) |
| S10 | DB credential repo'da (config.php) | 🟡 | Faz 1+14 (`.env` + `.env.example`) |
| S11 | `password_hash` / bcrypt yok | 🟡 | Faz 6 (Auth) |
| S12 | `$_SERVER['REQUEST_URI']` direkt okuma | 🟡 | Faz 2 (Request::capture) |

### ✅ Legacy'de doğru olanlar (korunacak)
- MVC ayrışımı prensibi doğru
- `.htaccess` URL rewriting + `Options -Indexes`
- `pure.css` responsive grid

---

## Per-issue detay (legacy)

### S1: Deprecated `mysql_*` extension

- **Severity:** 🔴 Critical
- **Lens:** code
- **File:** `system/model.php:70,80`
- **Symptom:** Proje PHP 7+ ortamda çalışmaz. `mysql_connect()`, `mysql_query()`, `mysql_fetch_object()` PHP 7'de **kaldırıldı**.
- **Fix (rewrite):** Eloquent ORM capsule (`illuminate/database`) ile değiştir — Faz 4.
- **Impact:** Proje modern PHP'de çalıştırılamaz.

### S3: SQL injection

- **Severity:** 🔴 Critical
- **Lens:** security
- **File:** `application/models/example_model.php:7`
- **Symptom:** `$this->query('SELECT * FROM something WHERE id="'. $id .'"')`
- **Fix (rewrite):** Eloquent prepared statements — Faz 4.
- **Impact:** Tüm DB dump alınabilir.

### S7: XSS (output escaping yok)

- **Severity:** 🔴 Critical
- **Lens:** security
- **File:** `system/view.php:22`
- **Symptom:** Raw `echo` ile template render — htmlspecialchars yok.
- **Fix (rewrite):** Blade `{{ }}` auto-escaping — Faz 5.

### S8: DB error ekrana dökülür

- **Severity:** 🔴 Critical
- **Lens:** qa
- **File:** `system/model.php:70`
- **Symptom:** `or die('MySQL Error: '. mysql_error())` — ham hata kullanıcıya.
- **Fix (rewrite):** Monolog + Exception Handler — Faz 8.

### S4: `extract()` view'de

- **Severity:** 🟡 Important
- **Lens:** code
- **File:** `system/view.php:20`
- **Symptom:** `extract($this->pageVars)` — değişken çakışması riski.
- **Fix (rewrite):** Blade template engine — Faz 5.

---

## Lens özetleri

### Code lens
Legacy PHP 5.3 standartlarında. `mysql_*` ve `create_function()` deprecated. Yeniden yazım kararı doğru.

### Security lens
SQL injection, XSS, CSRF yok, session güvenliği yok, password düz metin. Tümü plan kapsamında.

### QA lens
Sıfır test, `or die()` hata yönetimi. Faz 11'de Pest 3 + PHPStan 8 hedefleniyor.

### UX lens
Tek sayfa, Pure CSS. Hedef stack'te Blade + Tailwind + Alpine.js ile modern UI.
