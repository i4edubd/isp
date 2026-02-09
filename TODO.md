# ✅ Developer TODO Checklist for ISP Bills 

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
- [ ] Validate middleware, guards, and authentication flows.
- [ ] Update route definitions to match Laravel 12 conventions.

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
- [ ] Validate **FreeRADIUS** integration for PPPoE and Hotspot.
- [ ] Implement **WebAuthn** for passwordless login. (Migrations exist, implementation pending)
- [ ] Enforce MAC binding and duplicate session prevention.
- [ ] Test router → RADIUS → Laravel flow for PPPoE and Hotspot.

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
- [] Implement **PestPHP** or PHPUnit tests.
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


## 📂 Sample Files

A few sample configurations and reference files have been uploaded to the repository.  
You can find them here: [sample/](https://github.com/i4edubd/isp/tree/main/sample)

> ⚠️ **Note**: These samples are for demonstration and testing purposes only.  
> Do not use them in production without adapting to your own environment and credentials.
