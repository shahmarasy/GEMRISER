# Verification — GEMRISER 2.0

**Tarih:** 2026-05-18
**Mode:** code → full rewrite

> Bu dosya **gate** dosyasıdır. Hiçbir critical task `[x]` olamaz ilgili scenario `✅ Passed` olmadan.

## Test suite snapshot (mevcut)

- **Toplam test:** 0
- **Geçen:** 0
- **Kalan:** 0
- **Coverage:** N/A

```
Legacy'de test yok. Faz 11'de Pest 3 kurulacak, coverage ≥%70 hedef.
```

## E2E Scenarios

### Scenario S0: Legacy arşivlendi, monorepo hazır

- **Covers:** Task 0
- **Steps:**
  1. `git branch -a` — `legacy/v0.5.3` remote'da görünür
  2. Master'da `application/`, `system/`, `static/`, `index.php`, `.htaccess` yok
  3. `composer validate` root + her iki paket temiz
  4. `tree -L 3 -I vendor` — monorepo yapısı doğru
- **Expected:** Tüm adımlar başarılı.
- **Status:** ⏳ Pending

### Scenario S1: Demo app ayağa kalkar

- **Covers:** Task 1, 2, 3, 4, 5, 6, 7, 8, 9, 13
- **Pre-requisites:** PHP 8.3, MySQL, Composer
- **Steps:**
  1. `composer install`
  2. `cp .env.example .env` + `./gemcli key:generate`
  3. `./gemcli migrate --seed`
  4. `./gemcli serve`
  5. Browser: anasayfa → register → login → CRUD → logout
  6. `curl http://localhost:8000/api/examples` → JSON
  7. `curl -X POST http://localhost:8000/login -d 'email=x&password=y'` → 419 (CSRF)
  8. `curl http://localhost:8000/nonexistent` → 404 custom view
- **Expected:** Tüm akışlar çalışır, hata sayfaları görünür, CSRF koruması aktif.
- **Status:** ⏳ Pending

### Scenario S2: XSS + SQL injection kapalı

- **Covers:** Task 4, Task 5
- **Steps:**
  1. View'da `{{ '<script>alert(1)</script>' }}` → kaynak kodda `&lt;script&gt;`
  2. Eloquent query log'da prepared statement kontrolü
  3. `grep -rn 'extract(' packages/` boş
- **Expected:** XSS escape edilir. SQL injection imkansız. `extract()` yok.
- **Status:** ⏳ Pending

### Scenario S3: CI + Release pipeline

- **Covers:** Task 10, 11, 12, 13, 14
- **Steps:**
  1. `docker compose up -d --build` → 6 servis ayakta
  2. `npm run build` → `public/build/manifest.json` üretir
  3. `composer ci` (pint + phpstan + test) yeşil
  4. Tag push → GitHub Release + Packagist güncellemesi
  5. `composer create-project gemriser/skeleton /tmp/test-app` çalışır
  6. `git ls-files | grep '\.env$'` boş
- **Expected:** CI yeşil, subtree-split çalışır, Packagist'te yeni sürüm.
- **Status:** ⏳ Pending

---

## Manual verification checklist

- [ ] Legacy branch `legacy/v0.5.3` tüm commit geçmişini koruyor mu?
- [ ] Skeleton demo app register → login → CRUD → logout flow'u manuel test et
- [ ] Mobile responsive (Tailwind ile) düzgün mü?
- [ ] `APP_DEBUG=false` ortamda hata sayfaları stack trace içermiyor mu?
- [ ] `npm run build` production asset'leri doğru hash'liyor mu?
- [ ] Packagist'te her iki paket (`framework`, `skeleton`) görünüyor mu?

---

## Gate kararı (plan)

| Kategori | Durum |
|----------|-------|
| 🔴 Critical legacy bulgu kapatma | 0/12 (S1–S12) |
| E2E scenario yeşil | 0/4 |
| Test suite | ❌ (0 test) |

**Sonuç:** ❌ Henüz başlanmadı. 15 faz ~9.25 gün. Faz 0'dan başlanacak.
