# DomainChecker

A Laravel-based application for monitoring domain availability and status over time. Supports background queue processing, scheduled checks, and a web dashboard for managing domains and viewing check history.

---

## Features

- Domain registration and management per user
- Manual and automated domain checks
- Background job processing via Laravel queues
- Scheduled domain checking via Laravel scheduler
- Historical data storage for domain status changes
- Telegram notifications when a domain goes down
- Authenticated user dashboard

---

## Architecture

| Layer | Responsibility |
|---|---|
| HTTP | Controllers, Blade views, routing |
| Service | Domain check logic (`DomainCheckService`) |
| Queue | Async job processing (`CheckDomainJob`) |
| Scheduler | Periodic checks via Artisan scheduler |
| Persistence | Users, domains, check history in MySQL |

---

## Tech Stack

- PHP 8.3+
- Laravel 12+
- MySQL
- Laravel Queue (database driver)
- Laravel Scheduler
- Vue 3 + Blade
- Vite

---

## Installation

```bash
git clone https://github.com/nuxteam/DomainChecker.git
cd DomainChecker/src

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
php artisan migrate
```

---

## Environment

Copy `.env.example` to `.env` and configure:

```env
APP_NAME=DomainChecker
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=domainchecker
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=database
```

---

## Running

```bash
# Web server
php artisan serve

# Queue worker
php artisan queue:work --tries=3 --sleep=3

# Scheduler
php artisan schedule:work
```

All three processes must run simultaneously in production.

---

## Background Processing

```
User adds domain
      ↓
Job dispatched to queue
      ↓
Worker processes check
      ↓
Result stored in DB
      ↓
Telegram alert if status changed (up → down)
```

---

## API Routes

All routes require authentication.

| Method | Route | Description |
|---|---|---|
| `GET` | `/dashboard` | User dashboard |
| `POST` | `/domains` | Create domain |
| `PUT` | `/domains/{domain}` | Update domain |
| `DELETE` | `/domains/{domain}` | Delete domain |
| `POST` | `/domains/{domain}/check` | Manual check |
| `GET` | `/domains/{domain}/history` | Check history |

---

## Deployment

Compatible with Railway, Render, Fly.io, and any Docker-based environment.

Queue worker and scheduler must run as separate persistent processes in production. Use Supervisor or platform-level process management.

```ini
[program:laravel-worker]
command=php /var/www/artisan queue:work --tries=3 --sleep=3
autostart=true
autorestart=true

[program:laravel-scheduler]
command=php /var/www/artisan schedule:work
autostart=true
autorestart=true
```

---

## License

MIT
