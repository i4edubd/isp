# Developer Onboarding — ISPbills

Quick reference to get a developer environment running and to run the new RouterManagementService tests.

Prerequisites
- PHP 8.2+ with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`.
- Composer (latest stable)
- Node.js LTS + npm or yarn
- Docker & Docker Compose (optional, recommended for consistency)

Local setup
1. Copy `.env.example` to `.env` and update DB/SMS/payment credentials.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend deps and build (for UI):

```bash
npm install
npm run dev
```

4. Run migrations & seed (local/test DB):

```bash
php artisan migrate --seed
```

Running tests
- Unit tests (single file):

```bash
./vendor/bin/phpunit tests/Unit/RouterManagementServiceTest.php
```

- Full test suite:

```bash
./vendor/bin/phpunit
```

Notes on the RouterManagementService
- File: `app/Services/RouterManagementService.php`
- Purpose: encapsulates MikroTik RouterOS interactions (provisioning, suspend/resume, firewall list handling) with centralized error logging and optional client injection for tests.
- Testing: the service accepts an optional RouterOS `Client` instance to allow unit tests using a fake client.

Useful artisan commands
- Run the expiration handler (suspend/notify):

```bash
php artisan billing:handle-expirations
```

Troubleshooting
- If Router tests fail locally, ensure the injected fake client is used (see `tests/Unit/RouterManagementServiceTest.php`).
- If real routers are unavailable, avoid running router integration code against production devices.

Next steps
- Add CI job to run PHPUnit and static analysis (`phpstan`/`larastan`, `php-cs-fixer`).
