# GEMRISER

Modern micro PHP framework — spiritual successor to Lumen.

[![CI](https://github.com/gemriser/gemriser/actions/workflows/ci.yml/badge.svg)](https://github.com/gemriser/gemriser/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4)
![License](https://img.shields.io/badge/license-GPL--2.0-blue)

## Quick Start

```bash
composer create-project gemriser/skeleton my-app
cd my-app
cp .env.example .env
./gemcli key:generate
./gemcli migrate --seed
./gemcli serve
```

Visit `http://localhost:8000` — register, login, and explore.

## Architecture

```
Browser → Nginx → PHP-FPM → Kernel → Middleware → Router → Controller
                                                          ↓
                                              Eloquent ← Model
                                              Blade   ← View
```

## Stack

| Layer | Technology |
|-------|-----------|
| PHP | 8.3, strict types, PSR-7/15 |
| DI | illuminate/container |
| HTTP | nyholm/psr7 + relay/relay |
| Routing | FastRoute + named routes |
| Database | Eloquent ORM (illuminate/database) |
| Views | Blade (illuminate/view) |
| Auth | bcrypt + session guard + CSRF |
| CLI | Symfony Console (gemcli) |
| Logging | Monolog |
| Validation | illuminate/validation |
| Frontend | Vite + Tailwind + Alpine.js |
| DevOps | Docker Compose, GitHub Actions |

## Packages

- **gemriser/framework** — the micro framework library
- **gemriser/skeleton** — starter application skeleton

## Coming from Lumen?

GEMRISER follows the same architecture principles as Lumen:
- Same Eloquent ORM and Blade template engine
- Similar service provider pattern
- `gemcli` commands mirror `artisan` commands
- `.env` configuration format
- PSR-7 request/response (Lumen uses Symfony HTTP Foundation)

## License

GEMRISER is open-sourced software licensed under the [GPL-2.0](LICENSE).
