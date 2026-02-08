# ✅ Extended Developer Checklist for ISP Bills Refactor



## 📂 Sample Files

A few sample configurations and reference files have been uploaded to the repository.  
You can find them here: [sample/](https://github.com/i4edubd/isp/tree/main/sample)

### 🔧 Usage Notes
- **Local Testing**: Use these samples as seed data when setting up a local development environment.  
  Example: import sample SQL files into your test database to quickly validate schema and billing logic.
- **Configuration Reference**: Review sample router and RADIUS configs to understand expected integration points.  
  These can be adapted for your own MikroTik or FreeRADIUS setup.
- **Workflow Validation**: Apply sample customer and billing records to simulate daily/monthly billing cycles.  
  This helps confirm expiry notifications, package termination, and commission splits.
- **Panel Demonstration**: Use sample accounts to test role-based access across the eight panels (Admin, Reseller, Sub-reseller, etc.).
- **SMS/Payment Gateways**: Samples include request/response formats for gateway APIs.  
  Developers can use these as templates when implementing Maestro, bKash, Nagad, Rocket, and other integrations.

> ⚠️ **Note**: These samples are for demonstration and testing purposes only.  
> Do not use them in production without adapting to your own environment and credentials.


#### Models

Eloquent ORM models represent database tables.

**Core Models**:
- `User` - System users (admins, operators, customers)
- `Customer` - Customer information
- `BillingProfile` - Billing cycle configurations
- `CustomerPayment` - Payment records
- `CustomerBill` - Generated bills
- `Router` - Mikrotik router configurations
- `Package` - Internet packages
- `Account` - Accounting entries

#### Relationships

```php
// Example relationships
User -> hasMany -> Customers
Customer -> belongsTo -> Package
Customer -> hasMany -> Payments
Customer -> hasMany -> Bills
Router -> hasMany -> Customers
BillingProfile -> hasMany -> Customers
```

#### Observers

Monitor model events for automated actions.

**Example**: `OperatorObserver` - Auto-create support departments

### 5. Database Layer

Notes
Before adding sid, mgid, gid, or operator_id columns in migrations, confirm their necessity.

#### MySQL Database

Primary application database.

**Key Tables**:
- User management: `users`, `operators`
- Customer management: `all_customers`, `customer_complains`
- Billing: `billing_profiles`, `customer_bills`, `customer_payments`
- Network: `routers`, `packages`, `ipv4_pools`, `ipv6_pools`
- Accounting: `accounts`, `cash_ins`, `cash_outs`
- System: `activity_logs`, `device_monitors`

#### FreeRADIUS Database

Separate database for RADIUS authentication.

**Key Tables**:
- `radcheck` - User authentication
- `radreply` - User attributes
- `radacct` - Accounting/usage records
- `radgroupcheck` - Group authentication
- `radgroupreply` - Group attributes

#### Redis

In-memory data store for caching and queues.

**Usage**:
- Session storage
- Cache storage
- Queue backend
- Real-time data

## Directory Structure

```
IspBill/
├── app/
│   ├── Console/          # Artisan commands
│   │   └── Commands/     # Custom commands
│   ├── Events/           # Event classes
│   ├── Exceptions/       # Exception handling
│   ├── Http/
│   │   ├── Controllers/  # Request handlers
│   │   ├── Middleware/   # Request filters
│   │   └── Requests/     # Form validation
│   ├── Jobs/             # Queue jobs
│   ├── Listeners/        # Event listeners
│   ├── Mail/             # Mailable classes
│   ├── Models/           # Eloquent models
│   ├── Observers/        # Model observers
│   ├── Policies/         # Authorization policies
│   ├── Providers/        # Service providers
│   ├── Services/         # Business logic services
│   └── Traits/           # Reusable traits
├── bootstrap/            # Framework bootstrap
├── config/               # Configuration files
├── database/
│   ├── factories/        # Model factories
│   ├── migrations/       # Database migrations
│   │   ├── mysql/        # MySQL migrations
│   │   └── pgsql/        # PostgreSQL migrations
│   └── seeders/          # Database seeders
├── public/               # Web root
│   ├── doc/              # User documentation
│   └── themes/           # Frontend themes
├── resources/
│   ├── css/              # CSS files
│   ├── js/               # JavaScript files
│   ├── lang/             # Language files
│   └── views/            # Blade templates
├── routes/               # Route definitions
│   ├── api.php           # API routes
│   ├── web.php           # Web routes
│   ├── ajax.php          # AJAX routes
│   └── auth.php          # Auth routes
├── storage/              # Generated files
│   ├── app/              # Application files
│   ├── framework/        # Framework cache
│   └── logs/             # Log files
├── tests/                # Test files
│   ├── Feature/          # Feature tests
│   └── Unit/             # Unit tests
└── vendor/               # Composer dependencies
```

Details see **ARCHITECTURE.md**.


## 1. Environment Setup
- [x] Install **PHP 8.2+** with required extensions (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`).
- [x] Configure **Composer** to latest stable version.
- [x] Install **Node.js LTS** and **npm/yarn** for frontend builds.
- [x] Ensure **Vite 5.x** is installed and integrated with Laravel.
- [x] Verify **Tailwind CSS 3.x** setup with PostCSS and autoprefixer.

---

## 2. Laravel 12.x Upgrade
- [x] Update `composer.json` to require `laravel/framework: ^12.0`.
- [x] Run `composer update` and resolve dependency conflicts.
- [x] Refactor deprecated helpers (`str_*`, `array_*`) to use `Illuminate\Support\Str` and `Arr`.
- [x] Validate middleware, guards, and authentication flows.
- [x] Update route definitions to match Laravel 12 conventions.

---

## 3. Eight Panels Implementation
- [ ] **Developer Panel (Platform Owner)**: Full system control, architecture, and deployment.
- [ ] **Super Admin Panel (Partners Portal)**: Manage partner ISPs, commissions, and integrations.
- [ ] **Admin Panel (ISP Portal)**: Customer lifecycle, billing, router integration.
- [ ] **Reseller Panel**: Manage assigned customers, prepaid/postpaid billing.
- [ ] **Sub-reseller Panel**: Sub-distribution of packages, commission tracking.
- [ ] **Manager Panel**: Operational oversight, reporting, and staff management.
- [ ] **Card Distributors Panel (Recharge Point)**: Recharge card issuance, tracking, and reconciliation.
- [ ] **Customer Panel**: Self-service billing, payments, and service status.

---

## 4. Authentication & AAA
- [x] Validate **FreeRADIUS** integration for PPPoE and Hotspot.
- [ ] Implement **WebAuthn** for passwordless login. (Migrations exist, implementation pending)
- [x] Enforce MAC binding and duplicate session prevention.
- [x] Test router → RADIUS → Laravel flow for PPPoE and Hotspot.

---

## 5. Billing & Payments
- [x] Implement daily vs monthly billing cycles.
- [x] Ensure prepaid/postpaid logic consistency.
- [ ] Validate commission splits across reseller hierarchy.
- [ ] Add SQL constraints to prevent duplicate bills/payments.
- [ ] Test invoice generation (PDF/Excel).
- [x] **Monthly Billing Customers**: Auto-generate bills on the 1st of each month.
- [x] **Network Access Termination**: Ensure service is cut off immediately upon package expiry.

---

## 6. SMS Gateway Providers Integration
- [ ] Integrate and test each provider:
  - Maestro
  - Robi
  - M2M
  - BDBangladesh SMS
  - Bulk SMS BD
  - BTS SMS
  - 880 SMS
  - BD Smart Pay
  - ElitBuzz
  - SSL Wireless
  - ADN
  - SMS24
  - SMS BDSMS NetBrand
  - SMSMetrotel
  - DianaHostSMS in BD
  - Dhaka Soft BD
- [ ] Standardize API wrapper for SMS sending.
- [ ] Add fallback mechanism if one provider fails.
- [ ] Log all SMS transactions for audit.
- [x] **Customer Notifications**: Send SMS before account expiry.

---

## 7. Payment Gateway Integration
- [ ] **Local Gateways**:
  - bKash (Checkout, Tokenized Checkout, Standard Payment)
  - Nagad Mobile Financial Service
  - Rocket Mobile Financial Service
  - SSLCommerz Aggregator
  - aamarPay Aggregator
  - shurjoPay Aggregator
- [ ] **International/Regional Gateways**:
  - Razorpay
  - EasyPayWay Aggregator
  - Walletmix Aggregator
  - BD Smart Pay Aggregator Service
- [ ] **Manual/Other**:
  - Recharge Card
  - Send Money
- [ ] Implement unified payment interface for all gateways.
- [ ] Add webhook handling for payment confirmation.
- [ ] Ensure PCI-DSS compliance for sensitive data.
- [ ] Test refunds, partial payments, and reconciliation.
- [ ] **Customer Online Activation**: Enable service activation upon successful online payment.
- [ ] **Reseller/Sub-reseller Balance**: Allow online balance top-up.
- [ ] **Recharge Card Partners**: Enable online balance addition.

---

## 8. Router & Network Integration
- [x] Refactor MikroTik API calls into modular services.
- [ ] Move hardcoded IP ranges/firewall rules into config files.
- [x] Add error handling for router API failures.
- [x] Validate suspended user blocking via firewall rules.
- [x] Test PPPoE and Hotspot provisioning end-to-end.

---

## 9. Database Schema & Integrity
- [ ] Add foreign key constraints for customer–bill–payment relationships.
- [ ] Enforce unique indexes for usernames, MAC addresses, and IPs.
- [ ] Run migrations to clean deprecated fields.
- [x] Document schema with ERD diagrams.

---

## 10. Frontend & UX
- [x] Align dashboards with Metronic demo1.
- [x] Ensure role-based visibility of menus and charts.
- [ ] Validate Chart.js and Mapael integrations.
- [x] Refactor Axios calls to standardized API endpoints.
- [ ] **Customer Registration**: Implement mobile phone number registration flow.

---

## 11. Testing & CI/CD
- [x] Implement **PestPHP** or PHPUnit tests.
- [ ] Add frontend tests with Vitest/Jest.
- [ ] Run static analysis with PHPStan/Larastan.
- [ ] Enforce coding standards with PHP-CS-Fixer.
- [ ] Configure CI/CD pipeline for automated builds and tests.

---

## 12. Documentation
- [x] Update developer onboarding guide with stack requirements.
- [x] Document Vite + Tailwind build process.
- [x] Provide migration notes for Laravel 12 changes.
- [x] Maintain Markdown checklists for each module.
