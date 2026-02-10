# ISP Bills

ISP Bills is a full-stack SaaS solution for Internet Service Providers (ISPs).  
It centralizes customer management, billing, router integration, and multi-role access panels.

## 🚀 Getting Started
To understand the system design, role hierarchy, and integration points, please review the detailed architecture guide:

➡️ [ARCHITECTURE.md](https://github.com/i4edubd/isp/blob/main/ARCHITECTURE.md)

This document covers:
- Eight role-based panels (Developer, Super Admin, Admin, Reseller, Sub-reseller, Manager, Card Distributor, Customer).
- Authentication and AAA via FreeRADIUS and MikroTik API.
- Billing models (daily/monthly, prepaid/postpaid).
- SMS and payment gateway integrations.
- Database schema and workflow notes.
- Sample files for testing and configuration ([sample/](https://github.com/i4edubd/isp/tree/main/sample)).

## 🛠️ Developer Onboarding Guide

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js (LTS version)
- Docker and Docker Compose

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/i4edubd/isp.git
cd isp
```

2. **Environment setup**
```bash
cp .env.example .env
```
Update the `.env` file with your database credentials and other environment-specific settings.

3. **Install dependencies**
```bash
composer install
npm install
```

4. **Run database migrations**
```bash
php artisan migrate
```

5. **Start the development servers**
```bash
# In one terminal, start the Laravel server
php artisan serve

# In another terminal, start the Vite dev server
npm run dev
```

## Vite + Tailwind Build Process

This project uses Vite for frontend asset bundling and Tailwind CSS for styling.

- **Development**: Run `npm run dev` to start the Vite development server with Hot Module Replacement (HMR).
- **Production**: Run `npm run build` to compile and minify assets for production.

All frontend assets are located in the `resources` directory. The main CSS file is `resources/css/app.css`, and the main JavaScript file is `resources/js/app.js`. Tailwind CSS is configured in `tailwind.config.js`.

## Laravel 12 Migration Notes

This project has been upgraded to Laravel 12. Key changes to be aware of include:

- **New configuration options**: Review the `config` directory for new and updated configuration files.
- **Updated dependencies**: `composer.json` has been updated with the latest package versions.
- **API and method changes**: Some Laravel APIs and methods may have changed. Refer to the official [Laravel 12 release notes](https://laravel.com/docs/12.x/releases) for details.
- **Authentication**: The authentication scaffolding has been updated to use the latest Laravel standards. WebAuthn has been implemented for passwordless login.
