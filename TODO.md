# ✅ Developer TODO Checklist for ISP Bills 

## 1. Environment Setup
- [x] Install **PHP 8.2+** with required extensions (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`).
- [x] Configure **Composer** to latest stable version.
- [x] Install **Node.js LTS** and **npm/yarn** for frontend builds.
- [x] Ensure **Vite 7.x** is installed and integrated with Laravel.
- [x] Verify **Tailwind CSS 4.x** setup with PostCSS and autoprefixer.

---

## 2. Laravel 12.x Upgrade
- [x] All upgrade tasks completed, including dependency updates, refactoring, and validation of routes, middleware, and authentication flows.

---

## 3. Eight Panels Implementation
- [ ] **Developer Panel (Platform Owner)**:
  - [ ] Basic dashboard and layout created.
  - [ ] Implement full system control, architecture, and deployment features.
- [ ] **Super Admin Panel (Partners Portal)**:
  - [ ] Implement partner ISP management, commissions, and integrations.
- [ ] **Admin Panel (ISP Portal)**:
  - [ ] Implement customer lifecycle, billing, and router integration features.
- [ ] **Reseller Panel**:
  - [ ] Implement management of assigned customers, and prepaid/postpaid billing.
- [ ] **Sub-reseller Panel**:
  - [ ] Implement sub-distribution of packages, and commission tracking.
- [ ] **Manager Panel**:
  - [ ] Implement operational oversight, reporting, and staff management features.
- [ ] **Card Distributors Panel (Recharge Point)**:
  - [ ] Implement recharge card issuance, tracking, and reconciliation.
- [ ] **Customer Panel**:
  - [ ] Implement self-service billing, payments, and service status features.

---

## 4. Authentication & AAA
- [ ] Validate **FreeRADIUS** integration for PPPoE and Hotspot.
- [x] Implement **WebAuthn** for passwordless login.
  - [x] Backend routes and controller are in place.
  - [x] Created a test page for registration and login.
  - [x] Integrated WebAuthn into login and profile pages.
- [x] Enforce MAC binding and duplicate session prevention.
- [ ] Test router → RADIUS → Laravel flow for PPPoE and Hotspot.

---

## 5. Billing & Payments
- [ ] Implement daily vs monthly billing cycles.
- [ ] Ensure prepaid/postpaid logic consistency.
- [ ] Validate commission splits across reseller hierarchy.
- [ ] Add SQL constraints to prevent duplicate bills/payments.
- [ ] Test invoice generation (PDF/Excel).
- [x] **Monthly Billing Customers**: Auto-generate bills on the 1st of each month.
- [x] **Network Access Termination**: Ensure service is cut off immediately upon package expiry.

---

## 6. SMS Gateway Providers Integration
- [ ] Integrate and test each provider:
  - [ ] Maestro
  - [ ] Robi
  - [ ] M2M
  - [ ] BDBangladesh SMS
  - [ ] Bulk SMS BD
  - [ ] BTS SMS
  - [ ] 880 SMS
  - [ ] BD Smart Pay
  - [ ] ElitBuzz
  - [ ] SSL Wireless
  - [ ] ADN
  - [ ] SMS24
  - [ ] SMS BDSMS NetBrand
  - [ ] SMSMetrotel
  - [ ] DianaHostSMS in BD
  - [ ] Dhaka Soft BD
- [ ] Standardize API wrapper for SMS sending.
- [ ] Add fallback mechanism if one provider fails.
- [ ] Log all SMS transactions for audit.
- [ ] **Customer Notifications**: Send SMS before account expiry.

---

## 7. Payment Gateway Integration
- [ ] **Local Gateways**:
  - [ ] bKash (Checkout, Tokenized Checkout, Standard Payment)
  - [ ] Nagad Mobile Financial Service
  - [ ] Rocket Mobile Financial Service
  - [ ] SSLCommerz Aggregator
  - [ ] aamarPay Aggregator
  - [ ] shurjoPay Aggregator
- [ ] **International/Regional Gateways**:
  - [ ] Razorpay
  - [ ] EasyPayWay Aggregator
  - [ ] Walletmix Aggregator
  - [ ] BD Smart Pay Aggregator Service
- [ ] **Manual/Other**:
  - [ ] Recharge Card
  - [ ] Send Money
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
- [x] Move hardcoded IP ranges/firewall rules into config files.
- [x] Add error handling for router API failures.
- [x] Validate suspended user blocking via firewall rules.
- [ ] Test PPPoE and Hotspot provisioning end-to-end.

---

## 9. Database Schema & Integrity
- [ ] Add foreign key constraints for customer–bill–payment relationships.
- [ ] Enforce unique indexes for usernames, MAC addresses, and IPs.
- [ ] Run migrations to clean deprecated fields.
- [ ] Document schema with ERD diagrams.

---

## 10. Frontend & UX
- [ ] Align dashboards with Metronic demo1.
- [ ] Ensure role-based visibility of menus and charts.
- [ ] Validate Chart.js and Mapael integrations.
- [ ] Refactor Axios calls to standardized API endpoints.
- [ ] **Customer Registration**: Implement mobile phone number registration flow.

---

## 11. Testing & CI/CD
- [ ] Implement **PestPHP** or PHPUnit tests.
- [ ] Add frontend tests with Vitest/Jest.
- [ ] Run static analysis with PHPStan/Larastan.
- [ ] Enforce coding standards with PHP-CS-Fixer.
- [ ] Configure CI/CD pipeline for automated builds and tests.

---

## 12. Documentation
- [ ] Update developer onboarding guide with stack requirements.
- [ ] Document Vite + Tailwind build process.
- [ ] Provide migration notes for Laravel 12 changes.
- [ ] Maintain Markdown checklists for each module.
- [ ] Create Postman collection for API endpoints.

---

## Recent updates (developer notes)
- Router integration: added `app/Services/RouterManagementService.php` — provides provisioning, suspend/resume, and safer query handling.
- Controller/Command integration: replaced direct `MikrotikService` usage in `app/Http/Controllers/MikrotikController.php` and `app/Console/Commands/SuspendExpiredCustomers.php` to use `RouterManagementService`.
- Tests: added `tests/Unit/RouterManagementServiceTest.php` (unit tests use an injectable fake client). Run via PHPUnit: `./vendor/bin/phpunit tests/Unit/RouterManagementServiceTest.php`.
- Docs: added developer onboarding (DEVELOPER_ONBOARDING.md) with setup and test instructions.

Please DO NOT copy logic from `/sample` into production — samples are reference-only as noted in ARCHITECTURE.md.


## CRITICAL CONSTRAINT: >  DO NOT copy implementation logic or full functions from the /sample directory.

The /sample folder should be used strictly for architecture reference only (e.g., checking directory structure, class naming styles, or file locations).

All logic for current tasks (MikroTik, RADIUS, Billing) must be written fresh to support my specific tech stack (PHP 8.2, Docker, Laravel, Metronic theme).

You can find them here: [sample/](https://github.com/i4edubd/isp/tree/main/sample)