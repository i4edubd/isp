
# IspBills Application Overview

## 🏗️ Architecture Overview

### Backend (Laravel)
- **Framework**: Built on Laravel (PHP).
- **Authentication**: Uses **FreeRADIUS** for AAA (Authentication, Authorization, Accounting).
- **Network Integration**: Communicates with **MikroTik routers** via RouterOS API.
- **Database**: Relational DB with migrations; supports **WebAuthn** for passwordless login.
- **Controllers**: Examples like `RadreplyController.php` and `RouterConfigurationController.php` handle router communication, IP/VLAN management, and AAA logic.

### Frontend (Metronic + JS Libraries)
- **Theme**: Metronic for admin/reseller dashboards.
- **Libraries**:
  - jQuery (DOM manipulation)
  - Chart.js (traffic/payment visualizations)
  - Alpine.js (interactivity)
  - Axios (API calls)
  - jQuery Mapael (maps for customer locations/network coverage)

---

## 🔑 Core Features
- **Customer Management**: Supports PPPoE, Hotspot, and Static IP users.
- **Billing & Payments**: Generates invoices (PDF/Excel), supports cash, online, and recharge cards.
- **Network Management**: Direct MikroTik integration for traffic monitoring and router configuration.
- **Role-Based Panels**: Separate dashboards for Admin, Reseller, Sub-Reseller, Manager, and Customer.

---

## 🔐 Authentication Models

### PPPoE
- Username/password stored in RADIUS.
- Router forwards credentials → RADIUS verifies → assigns IP/bandwidth.
- Supports MAC binding for security.

### Hotspot
- Self-registration via mobile number.
- Device MAC captured and used as credential.
- Seamless reconnection without manual login.

---

## 💰 Billing Models

| Feature          | PPPoE + Daily Billing | PPPoE + Monthly Billing |
|------------------|-----------------------|-------------------------|
| Cycle            | Flexible (7–15 days)  | Fixed monthly           |
| Bill Generation  | Manual recharge       | Auto-generated on 1st   |
| Payment          | Strictly prepaid      | Prepaid or postpaid     |
| Use Case         | Short-term reseller customers | Standard monthly subscribers |

---

## 👥 Reseller & Sub-Reseller Model
- **Hierarchy**: Admin → Reseller → Sub-Reseller → Customer.
- **Commission**: Automated revenue split across levels.
- **Billing**: Supports prepaid/postpaid, daily/monthly cycles.
- **Limitations**: Resellers can’t create packages or routers; only manage assigned customers.

---

## 📊 Database Schema (Simplified)
- **customers**: username, password, connection type, package, billing profile, status, expiration.
- **customer_bills**: amount, bill_date, due_date, status, customer_id.
- **customer_payments**: amount, method, customer_id, operator_id, timestamps.

---

## 🔧 MikroTik API Integration
- Automates router setup:
  - Configures RADIUS for PPPoE & Hotspot.
  - Sets firewall rules (e.g., block suspended users).
  - Manages hotspot profiles, PPPoE sessions, duplicate session handling.
- **Code Quality Suggestions**:
  - Refactor long methods into services.
  - Move hardcoded values (e.g., IP ranges) into config files.
  - Improve error handling for API user checks.

---

## 📌 Summary
**IspBills** is a full-stack ISP SaaS platform that centralizes customer management, billing, and router control.  
It leverages Laravel + RADIUS for backend logic, Metronic + JS libraries for frontend dashboards, and MikroTik API for direct network enforcement.


# ISPbills System Architecture

This document provides an overview of the ISPbills system architecture, design patterns, and technical structure.


## Table of Contents

- [Overview](#overview)
- [Technology Stack](#technology-stack)
- [Architecture Layers](#architecture-layers)
- [Directory Structure](#directory-structure)
- [Design Patterns](#design-patterns)
- [Database Schema](#database-schema)
- [Security Architecture](#security-architecture)
- [Integration Points](#integration-points)
- [Scalability Considerations](#scalability-considerations)

## Overview

ISPbills is built on the Laravel framework, following the MVC (Model-View-Controller) architectural pattern with additional layers for business logic, data access, and external integrations.

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                         Client Layer                            │
│  ┌─────────────┐  ┌──────────────┐  ┌────────────────────┐    │
│  │  Web Browser │  │ Mobile Apps  │  │  External Systems  │    │
│  └─────────────┘  └──────────────┘  └────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Application Layer                          │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │  Nginx / Apache (Web Server)                             │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Laravel Layer                           │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐    │
│  │  Controllers  │  │  Middleware   │  │  API Routes      │    │
│  └──────────────┘  └───────────────┘  └──────────────────┘    │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐    │
│  │  Models       │  │  Policies     │  │  Events/Listeners│    │
│  └──────────────┘  └───────────────┘  └──────────────────┘    │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐    │
│  │  Jobs/Queues  │  │  Services     │  │  Observers       │    │
│  └──────────────┘  └───────────────┘  └──────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                        Data Layer                               │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐    │
│  │  MySQL DB     │  │  Redis Cache  │  │  FreeRADIUS DB   │    │
│  └──────────────┘  └───────────────┘  └──────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                   External Services Layer                       │
│  ┌──────────────┐  ┌───────────────┐  ┌──────────────────┐    │
│  │  Mikrotik     │  │  SMS Gateway  │  │  Payment Gateway │    │
│  │  Routers      │  │               │  │                  │    │
│  └──────────────┘  └───────────────┘  └──────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

## Technology Stack

### Backend (Laravel) 

- **Framework**: Built on Laravel (PHP). 
- **Authentication**: Uses **FreeRADIUS** for AAA (Authentication, Authorization, Accounting). 
- **Network Integration**: Communicates with **MikroTik routers** via RouterOS API. 
- **Database**: Relational DB with migrations; supports **WebAuthn** for passwordless login. 
- **Controllers**: Examples like `RadreplyController.php` and `RouterConfigurationController.php` handle router communication, IP/VLAN management, and AAA logic.

### Frontend (Metronic + JS Libraries) 

- **Theme**: Metronic for admin/reseller dashboards. 
- **Libraries**: 
- jQuery (DOM manipulation) 
- Chart.js (traffic/payment visualizations) 
- Alpine.js (interactivity) 
- Axios (API calls) 
- jQuery Mapael (maps for customer locations/network coverage) 

---

### DevOps

- **Containerization**: Docker, Docker Compose
- **Web Server**: Apache, Nginx
- **Process Manager**: Supervisor
- **Version Control**: Git

### Third-Party Integrations

- **Router Management**: Mikrotik RouterOS API
- **SMS**: Various SMS gateway providers
- **Payments**: bKash, Nagad, Rocket, PayPal, Stripe
- **Monitoring**: RRD (Round-Robin Database) for network graphs
- **QR Codes**: Bacon QR Code for payment/authentication

## Architecture Layers

### 1. Presentation Layer

**Location**: `resources/views/`, `public/`

- Blade templates for server-side rendering
- Metronic theme for admin interface
- Responsive design with Tailwind CSS
- AJAX for dynamic interactions

**Key Components**:
- Dashboard views
- Customer management interfaces
- Billing and payment forms
- Network monitoring displays

### 2. Application Layer

**Location**: `app/Http/`

#### Controllers

Organize request handling and coordinate between models and views.

**Structure**:
```
app/Http/Controllers/
├── AccountController.php
├── CustomerController.php
├── BillingProfileController.php
├── PaymentController.php
├── RouterController.php
├── Ajax/
│   └── AjaxController.php
└── Auth/
    └── AuthController.php
```

#### Middleware

Handle request filtering and preprocessing.

**Common Middleware**:
- Authentication (`auth`)
- Role/Permission checking
- CORS handling
- Rate limiting
- API token validation

#### Form Requests

Validate incoming requests before they reach controllers.

### 3. Business Logic Layer

**Location**: `app/Services/`, `app/Jobs/`, `app/Traits/`

#### Services

Encapsulate complex business logic.

**Examples**:
- `BillingService` - Handle billing calculations
- `PaymentProcessingService` - Process payments
- `RouterManagementService` - Manage router configurations
- `NotificationService` - Send notifications

#### Jobs

Background tasks for async processing.

**Common Jobs**:
- `GenerateMonthlyBills`
- `ProcessPaymentNotifications`
- `SyncOnlineCustomers`
- `SendBulkSMS`
- `AutoSuspendExpiredAccounts`

#### Events & Listeners

Decouple components through event-driven architecture.

**Events**:
- `CustomerRegistered`
- `PaymentReceived`
- `BillGenerated`
- `CustomerSuspended`

### 4. Data Access Layer

**Location**: `app/Models/`

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

## Design Patterns

### 1. MVC Pattern

Core Laravel pattern separating concerns:
- **Models**: Data and business logic
- **Views**: Presentation layer
- **Controllers**: Request handling

### 2. Repository Pattern

(Partially implemented) Abstracts data access:
```php
interface CustomerRepository {
    public function find($id);
    public function all();
    public function create(array $data);
}
```

### 3. Observer Pattern

Model observers for automated actions:
```php
class OperatorObserver {
    public function created(Operator $operator) {
        // Auto-create default departments
    }
}
```

### 4. Event-Listener Pattern

Decouples components:
```php
Event::listen(PaymentReceived::class, function($event) {
    // Send notification
    // Update ledger
    // Generate receipt
});
```

### 5. Factory Pattern

Used for testing and seeding:
```php
Customer::factory()->count(50)->create();
```

### 6. Strategy Pattern

Used for payment gateways and SMS providers:
```php
interface PaymentGateway {
    public function processPayment($amount);
}

class BkashGateway implements PaymentGateway { ... }
class NagadGateway implements PaymentGateway { ... }
```

### 7. Facade Pattern

Laravel facades for clean API:
```php
Cache::remember('key', $ttl, function() { ... });
Queue::push(new SendEmailJob);
```

## Database Schema

### Entity Relationship Overview

```
┌──────────────┐         ┌──────────────┐
│   Users      │────────▶│  Operators   │
└──────────────┘         └──────────────┘
                                │
                                │
                                ▼
┌──────────────┐         ┌──────────────┐
│   Packages   │◀────────│  Customers   │
└──────────────┘         └──────────────┘
                                │
                    ┌───────────┼───────────┐
                    ▼           ▼           ▼
            ┌──────────┐ ┌──────────┐ ┌──────────┐
            │  Bills   │ │ Payments │ │  Ledger  │
            └──────────┘ └──────────┘ └──────────┘
                                │
                                ▼
                         ┌──────────────┐
                         │   Routers    │
                         └──────────────┘
```

### Key Tables

**Users & Authentication**:
- `users` - System users
- `operators` - ISP operators/admins
- `authentication_logs` - Login history

**Customer Management**:
- `all_customers` - Customer records
- `customer_complains` - Support tickets
- `customer_change_logs` - Change history

**Billing & Payments**:
- `billing_profiles` - Billing cycle configs
- `customer_bills` - Generated bills
- `customer_payments` - Payment records
- `accounts` - Double-entry accounting

**Network**:
- `routers` - Mikrotik routers
- `packages` - Internet packages
- `ipv4_pools` - IP address pools
- `pppoe_profiles` - PPPoE configurations

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


## Security Architecture

### Authentication

- **Web**: Session-based with CSRF protection
- **API**: Token-based (Sanctum)
- **RADIUS**: FreeRADIUS with shared secrets

### Authorization

- **Policies**: Laravel policy classes for fine-grained permissions
- **Roles**: super_admin, group_admin, operator, sub_operator, customer
- **Middleware**: Role-based access control

### Data Protection

- **Encryption**: Sensitive data encrypted at rest
- **Hashing**: Passwords hashed with bcrypt
- **SQL Injection**: Protected by Eloquent ORM
- **XSS**: Protected by Blade templating
- **CSRF**: Token-based protection

### Network Security

- **Firewall Rules**: Mikrotik firewall automation
- **API Rate Limiting**: Prevent abuse
- **HTTPS**: SSL/TLS encryption
- **IP Whitelisting**: For API access (optional)

## Integration Points

### Mikrotik RouterOS

- API-based communication
- User management (PPPoE, Hotspot, Static IP)
- Bandwidth management
- Firewall rule automation
- Online user monitoring

### FreeRADIUS

- AAA (Authentication, Authorization, Accounting)
- PPPoE authentication
- Hotspot authentication
- Usage tracking
- Session management

### Payment Gateways

- bKash, Nagad, Rocket (Mobile banking)
- PayPal, Stripe (International)
- Manual/Cash payments

### SMS Gateways

- Bulk SMS sending
- OTP verification
- Bill notifications
- Payment confirmations

## Scalability Considerations

### Horizontal Scaling

- **Load Balancing**: Multiple web server instances
- **Database Replication**: Master-slave MySQL setup
- **Redis Cluster**: Distributed caching

### Vertical Scaling

- Upgrade server resources (CPU, RAM, Storage)
- Optimize database queries
- Implement caching strategies

### Performance Optimization

- **Caching**: Redis for frequently accessed data
- **Queue Workers**: Multiple queue workers for background jobs
- **Database Indexing**: Proper indexes on frequently queried columns
- **CDN**: Static asset delivery
- **Lazy Loading**: Defer loading of non-critical resources

### Monitoring

- **Application Logs**: `storage/logs/`
- **Database Monitoring**: Slow query logs
- **Server Metrics**: CPU, Memory, Disk usage
- **Network Monitoring**: RRD graphs, device status
- **Error Tracking**: Laravel Log Viewer

## Development Workflow

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Development │───▶│   Staging   │───▶│ Production  │
└─────────────┘    └─────────────┘    └─────────────┘
      │                   │                   │
      ▼                   ▼                   ▼
  Local Tests      Integration Tests    Monitoring
  Code Review         UAT Testing         Backups
  Git Commit         Performance         Security
```

## Best Practices

1. **Follow Laravel conventions**: Use standard Laravel patterns
2. **Keep controllers thin**: Move business logic to services
3. **Use queues**: Offload time-consuming tasks
4. **Cache aggressively**: Cache frequently accessed data
5. **Write tests**: Maintain test coverage
6. **Log important events**: Use activity logging
7. **Monitor performance**: Track slow queries and requests
8. **Document code**: Add comments for complex logic
9. **Version control**: Use Git for all changes
10. **Security first**: Regular security audits

## Future Improvements

- Microservices architecture for high-scale deployments
- GraphQL API for flexible data queries
- Real-time notifications with WebSockets
- Machine learning for usage prediction
- Mobile app development (iOS/Android)
- Multi-tenancy support

---

# ISP Bills - Complete Onboarding & Router Configuration Guide

## Table of Contents
1. [Onboarding Process Overview](#onboarding-process-overview)
2. [Minimum Configuration Requirements](#minimum-configuration-requirements)
3. [Adding a Router](#adding-a-router)
4. [Router Configuration](#router-configuration)
5. [Router Configuration Tasks](#router-configuration-tasks)
6. [Scheduler & Sync Operations](#scheduler--sync-operations)
7. [RADIUS Server Responsibilities](#radius-server-responsibilities)
8. [Import Operations](#import-operations)
9. [Form Fields Reference](#form-fields-reference)
10. [Developer Guide](#developer-guide)

---

## 1. Onboarding Process Overview

The ISP Bills onboarding process ensures that all necessary components are configured before operators can start managing customers. The system uses a guided workflow to verify completeness.

### Onboarding Flow
```
Registration → Exam (optional) → Billing Profile → Add Router → 
Configure Router → Import/Add Customers → Assign Billing Profiles → 
Assign Packages → Set Prices → Configure Backup Settings → 
Complete Profile → Dashboard Access
```

### Key Controller
**File:** `app/Http/Controllers/MinimumConfigurationController.php`

The `MinimumConfigurationController` orchestrates the onboarding by checking:
- Exam attendance (if enabled)
- Billing profile creation
- Router registration
- Customer data (imported or manually added)
- Billing profile assignments
- Package assignments and pricing
- Backup settings
- Operator profile completion

---

## 2. Minimum Configuration Requirements

### For Group Admin (Primary Account)

#### Step 1: Exam (Optional)
- **Condition:** If `config('consumer.exam_attendance')` is enabled
- **Required:** Pass the exam if questions exist
- **Route:** `exam.index`

#### Step 2: Billing Profile
- **Required:** At least one billing profile must be created
- **Check:** `billing_profile::where('mgid', $operator->id)->count() > 0`
- **Route:** `temp_billing_profiles.create`

#### Step 3: Router Registration
- **Required:** At least one router (NAS) must be added
- **Check:** Router count in `nas` table for operator
- **Route:** `routers.create`
- **Model:** `App\Models\Freeradius\nas`

#### Step 4: Customer Data
- **Required:** At least one customer OR an import request pending
- **Check:** Customer count OR import request exists
- **Route:** `pppoe_customers_import.create`

#### Step 5: Assign Billing Profile to Self
- **Required:** Operator must have assigned billing profile
- **Check:** `billing_profile_operator` record exists
- **Route:** `operators.billing_profiles.create`

#### Step 6: Assign Billing Profile to Resellers
- **Required:** All resellers (operators) must have billing profiles
- **Applies to:** Each operator under group admin

#### Step 7: Package Assignment
- **Required:** Packages must be created from master packages
- **Check:** Package count for operator
- **Route:** `operators.master_packages.create`

#### Step 8: Package Pricing
- **Required:** All packages must have price > 1 (except Trial)
- **Check:** Package price validation
- **Route:** `packages.edit`

#### Step 9: Backup Settings
- **Required:** Backup settings must be configured for all operators
- **Check:** `backup_setting` record exists
- **Route:** `backup_settings.create`
- **Purpose:** Defines primary router for authentication

#### Step 10: Profile Completion
- **Required:** Company name in native language must be set
- **Check:** `company_in_native_lang` field is not null
- **Route:** `operators.profile.create`

### For Operators (Resellers)
- Assign billing profiles to sub-operators
- Assign packages to sub-operators
- Set operator and customer prices for all packages

---

## 3. Adding a Router

### Prerequisites
- Group admin or developer account
- Router with MikroTik RouterOS
- API access enabled on router
- Router network connectivity to RADIUS server

### Router Information Required

#### Basic Information
- **NAS Name (nasname):** Router IP address (e.g., `192.168.1.1`)
- **Short Name (shortname):** Identifier for router (e.g., `Main-Router`)
- **Type:** Router type (usually `mikrotik`)
- **Secret:** Shared secret for RADIUS communication
- **Description:** Optional router description
- **Community:** SNMP community string (default: `billing`)
- **Operator ID:** Owner of the router

#### API Credentials
- **API Username (api_username):** RouterOS API user
- **API Password (api_password):** RouterOS API password
- **API Port (api_port):** Default `8728` for API, `8729` for API-SSL

### Adding Router via UI

**Route:** `/routers/create`

1. Navigate to Routers menu
2. Click "Add New Router"
3. Fill in the form fields:
   ```
   - NAS Name: [Router IP]
   - Short Name: [Identifier]
   - Type: mikrotik
   - Secret: [Shared Secret - Min 16 chars]
   - API Username: [API User]
   - API Password: [API Pass]
   - API Port: 8728
   - Community: billing
   ```
4. Submit the form
5. Router will be validated and saved

### Router Model Schema

**File:** `app/Models/Freeradius/nas.php`

**Table:** `nas`

**Key Fields:**
- `id`: Primary key
- `nasname`: Router IP address
- `shortname`: Router identifier
- `type`: Router type
- `secret`: RADIUS shared secret
- `api_username`: API username
- `api_password`: API password
- `api_port`: API port
- `community`: SNMP community
- `mgid`: Master group ID
- `gid`: Group ID
- `operator_id`: Owner operator ID

---

## 4. Router Configuration

Router configuration is the automated process of setting up a MikroTik router to work with the ISP Bills RADIUS server.

### Configuration Process

**Controller:** `app/Http/Controllers/RouterConfigurationController.php`

**Route:** `/routers/{router}/configuration/create`

### What Gets Configured

#### 1. RADIUS Settings
```routeros
/radius add
  address=[RADIUS Server IP]
  authentication-port=3612
  accounting-port=3613
  secret=[Router Secret]
  service=hotspot,ppp
  timeout=3s
  require-message-auth=no
```

#### 2. System Identity (Optional)
- Sets router identity based on operator and router settings
- Format: `{CompanyName}-{RouterShortname}`

#### 3. Firewall NAT Rules (Hotspot)
```routeros
/ip firewall nat add
  chain=pre-hotspot
  dst-address-type=!local
  hotspot=auth
  action=accept
  comment="bypassed auth"
```

#### 4. Walled Garden (Hotspot)
```routeros
/ip hotspot walled-garden ip add
  action=accept
  dst-address=[RADIUS Server IP]
  comment="Radius Server"
```

#### 5. Hotspot Server Settings
```routeros
/ip hotspot set
  idle-timeout=5m
  keepalive-timeout=none
  login-timeout=none
```

#### 6. Hotspot Profile Settings
```routeros
/ip hotspot profile set
  login-by=mac,cookie,http-chap,http-pap,mac-cookie
  mac-auth-mode=mac-as-username-and-password
  http-cookie-lifetime=6h
  split-user-domain=no
  use-radius=yes
  radius-accounting=yes
  radius-interim-update=5m
  nas-port-type=wireless-802.11
  radius-mac-format=XX:XX:XX:XX:XX:XX
```

#### 7. Hotspot User Profile Settings
```routeros
/ip hotspot user profile set
  idle-timeout=none
  keepalive-timeout=2m
  queue-type=hotspot-default
  on-login=[Priority Queue Script]
  on-logout=[Cleanup Script]
```

**On-Login Script:**
```routeros
:foreach n in=[/queue simple find comment=priority_1] do={
  /queue simple move $n [:pick [/queue simple find] 0]
}
```

**On-Logout Script:**
```routeros
/ip hotspot host remove [find where address=$address and !authorized and !bypassed]
```

#### 8. PPPoE Server Settings
```routeros
/ppp profile set default
  local-address=10.0.0.1

/pppoe-server server set
  authentication=pap,chap
  one-session-per-host=yes
  default-profile=default
```

#### 9. PPP AAA Settings
```routeros
/ppp aaa set
  interim-update=5m
  use-radius=yes
  accounting=yes
```

#### 10. PPP Profile On-Up Script
```routeros
:local sessions [/ppp active print count-only where name=$user];
:if ( $sessions > 1) do={
  :log info ("disconnecting " . $user  ." duplicate" );
  /ppp active remove [find where (name=$user && uptime<00:00:30 )];
}
```
*Purpose:* Disconnects duplicate PPPoE sessions

#### 11. Suspended Users Pool
```routeros
/ip pool add
  name=[Pool Name]
  ranges=100.65.96.0/20
```

#### 12. RADIUS Incoming
```routeros
/radius incoming set accept=yes
```
*Purpose:* Allows RADIUS to send commands to router (CoA/Disconnect)

#### 13. SNMP Configuration
```routeros
/snmp set enabled=yes
/snmp community add name=billing
```

#### 14. Firewall Rules for Suspended Pool
```routeros
/ip firewall filter add
  chain=forward
  src-address=100.65.96.0/20
  action=drop
  comment="drop suspended pool"

/ip firewall filter add
  chain=input
  src-address=100.65.96.0/20
  action=drop
  comment="drop suspended pool"
```

### Required API Permissions

The API user must have **full** or **write** permissions:
```routeros
/user group print
# Verify user group is 'full' or 'write'
```

---

## 5. Router Configuration Tasks

### Manual Router Queries

#### Check Online Status
**Controller:** `app/Http/Controllers/QueryInRouterController.php`

**Purpose:** Query router to check if customer is online

**Methods:**
- For PPPoE: Queries `/ppp active` for username
- For Hotspot: Queries `/ip hotspot active` for username

**Usage:**
```php
QueryInRouterController::getOnlineStatus($customer);
```

#### Transfer Customer to RADIUS
**Controller:** `app/Http/Controllers/RouterToRadiusController.php`

**Purpose:** Disable customer in router when transferring to RADIUS authentication

**Process:**
1. Connects to router via API
2. Finds customer in `/ppp secret`
3. Sets `disabled=yes`
4. Disconnects active sessions

**Usage:**
```php
RouterToRadiusController::transfer($router, $customer);
```

### Configuration Frequency

- **Initial Configuration:** Once per router during onboarding
- **Updates:** Manual or when router settings change
- **No automatic reconfiguration:** Configuration is applied once

---

## 6. Scheduler & Sync Operations

### Task Scheduler

**File:** `app/Console/Kernel.php`

The Laravel scheduler runs various tasks automatically via cron:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Router-Related Scheduled Tasks

#### 1. Check Routers API Status
**Command:** `check_routers_api`
**Frequency:** Daily
**Purpose:** Verify all routers are accessible via API

#### 2. Sync Online Customers
**Command:** `sync:online_customers {operator_id}`
**Frequency:** Manual (can be scheduled)
**File:** `app/Console/Commands/SyncOnlineCustomersWithApiCommand.php`

**What it does:**
1. Queries router for active PPPoE connections
2. Compares with RADIUS accounting data
3. Creates missing accounting records for online customers
4. Syncs discrepancies between router and RADIUS

**Usage:**
```bash
php artisan sync:online_customers 1
```

#### 3. RADIUS SQL Relay
**Command:** `rad:sql_relay_v2p`
**Frequency:** Every 3 minutes
**Purpose:** Process RADIUS accounting data

#### 4. Pull RADIUS Accounting
**Command:** `pull:radaccts`
**Frequency:** Every 5 minutes
**Purpose:** Retrieve and process accounting data

#### 5. Remove Stale Sessions
**Command:** `delete:rad_stale_sessions`
**Frequency:** Every 5 minutes
**Purpose:** Clean up orphaned accounting records

#### 6. Move RADIUS Accounting
**Command:** `move:radaccts`
**Frequency:** Every 15 minutes
**Purpose:** Archive old accounting records

#### 7. Restart FreeRADIUS
**Command:** `restart:freeradius`
**Frequency:** Every 2 hours
**File:** `app/Console/Commands/RestartFreeRadiusCommand.php`
**Purpose:** Restart RADIUS service to prevent memory leaks

### No Automatic Router Sync

**Important:** There is no automatic configuration sync from router to RADIUS. The system relies on:
- RADIUS authentication (customers authenticate against database)
- Scheduled sync commands for accounting data
- Manual import commands for bulk operations

---

## 7. RADIUS Server Responsibilities

### Overview

FreeRADIUS is the authentication, authorization, and accounting (AAA) server for ISP Bills.

### RADIUS Configuration

**Config File:** `resources/freeradius3x/radiusd.conf`

### Key Responsibilities

#### 1. Authentication
- **Purpose:** Verify customer credentials
- **Process:**
  1. Customer connects (PPPoE/Hotspot)
  2. Router sends authentication request to RADIUS
  3. RADIUS queries database (`radcheck`, `radreply`, `radgroupcheck`, `radgroupreply`)
  4. Returns Accept or Reject

#### 2. Authorization
- **Purpose:** Provide service parameters
- **Attributes returned:**
  - Rate limit (download/upload speed)
  - IP pool assignment
  - Session timeout
  - Data limits (total octets)
  - Suspended pool (for suspended customers)

#### 3. Accounting
- **Purpose:** Track usage and sessions
- **Tables:**
  - `radacct`: Active and completed sessions
  - `radpostauth`: Authentication attempts

**Accounting Process:**
1. **Start:** Customer connects, router sends Accounting-Start
2. **Interim-Update:** Every 5 minutes, router sends usage update
3. **Stop:** Customer disconnects, router sends Accounting-Stop

#### 4. Change of Authorization (CoA)
- **Purpose:** Dynamic customer management
- **Capabilities:**
  - Disconnect customer
  - Change rate limit
  - Update attributes

**Configuration:**
```routeros
/radius incoming set accept=yes
```

#### 5. SQL Integration
- **Purpose:** Store all data in MySQL/PostgreSQL
- **Tables:**
  - `radcheck`: Customer username/password
  - `radreply`: Customer-specific attributes
  - `radgroupcheck`: Group-level checks
  - `radgroupreply`: Group-level attributes
  - `radacct`: Accounting records
  - `nas`: Router definitions

### RADIUS Ports
- **Authentication:** 3612 (custom, standard is 1812)
- **Accounting:** 3613 (custom, standard is 1813)
- **CoA:** 3799 (standard)

### Customer Authentication Flow

```
Customer → Router → RADIUS → Database → RADIUS → Router → Customer
         (request)        (query)      (response)  (accept/reject)
```

---

## 8. Import Operations

### Import MikroTik Resources to Database

**Controller:** `app/Http/Controllers/Mikrotik/MikrotikDbSyncController.php`

**Purpose:** Import IP pools, PPP profiles, and PPP secrets from MikroTik router to MySQL database

**Process:**
The `MikrotikDbSyncController::sync()` method imports MikroTik resources when a customer import request is created. This happens automatically during the customer import process.

#### 1. Import IP Pools from MikroTik

**What Gets Imported:**
- IP pool name
- IP address ranges (converted to CIDR notation)

**RouterOS Path:** `/ip pool`

**Database Table:** `mikrotik_ip_pools`

**Fields:**
- `customer_import_request_id`: Links to import request
- `mgid`: Master group ID
- `operator_id`: Owner operator ID
- `nas_id`: Router ID
- `name`: Pool name from MikroTik
- `ranges`: IP ranges in CIDR format (e.g., `10.0.0.0/24`)

**Example:**
```php
// Import IP pools from router
$ip4pools = $api->getMktRows('ip_pool');

while ($ip4pool = array_shift($ip4pools)) {
    $ranges = MikrotikDbSyncController::parseIpPool($ip4pool['ranges']);
    
    $ip_pool = new mikrotik_ip_pool();
    $ip_pool->customer_import_request_id = $customer_import_request->id;
    $ip_pool->mgid = $customer_import_request->mgid;
    $ip_pool->operator_id = $customer_import_request->operator_id;
    $ip_pool->nas_id = $customer_import_request->nas_id;
    $ip_pool->name = $ip4pool['name'];
    $ip_pool->ranges = $ranges;
    $ip_pool->save();
}
```

**IP Range Parsing:**
The system automatically converts MikroTik IP pool ranges to standardized CIDR notation:
- Comma-separated ranges: `10.0.0.1-10.0.0.50,10.0.1.1-10.0.1.50` → `10.0.0.0/23`
- Slash notation: `192.168.1.0/24` → `192.168.1.0/24`
- Hyphen ranges: `172.16.0.1-172.16.0.254` → `172.16.0.0/24`

#### 2. Import PPP Profiles from MikroTik

**What Gets Imported:**
- PPP profile names
- Local address (gateway IP)
- Remote address (IP pool reference)

**RouterOS Path:** `/ppp profile`

**Database Table:** `mikrotik_ppp_profiles`

**Fields:**
- `customer_import_request_id`: Links to import request
- `mgid`: Master group ID
- `operator_id`: Owner operator ID
- `nas_id`: Router ID
- `name`: Profile name from MikroTik
- `local_address`: Gateway/server IP address
- `remote_address`: IP pool or address range for clients

**Example:**
```php
// Import PPP profiles from router (excludes default profile)
$ppp_profiles = $api->getMktRows('ppp_profile', ['default' => 'no']);

while ($ppp_profile = array_shift($ppp_profiles)) {
    $mikrotik_ppp_profile = new mikrotik_ppp_profile();
    $mikrotik_ppp_profile->customer_import_request_id = $customer_import_request->id;
    $mikrotik_ppp_profile->mgid = $customer_import_request->mgid;
    $mikrotik_ppp_profile->operator_id = $customer_import_request->operator_id;
    $mikrotik_ppp_profile->nas_id = $customer_import_request->nas_id;
    $mikrotik_ppp_profile->name = $ppp_profile['name'];
    $mikrotik_ppp_profile->local_address = $ppp_profile['local-address'] ?? '';
    $mikrotik_ppp_profile->remote_address = $ppp_profile['remote-address'] ?? '';
    $mikrotik_ppp_profile->save();
}
```

**Note:** The default profile is excluded from import as it's a system profile.

#### 3. Import PPP Secrets from MikroTik

**What Gets Imported:**
- PPPoE usernames
- Passwords
- Assigned profiles
- Comments (metadata)
- Disabled status

**RouterOS Path:** `/ppp secret`

**Database Table:** `mikrotik_ppp_secrets`

**Fields:**
- `customer_import_request_id`: Links to import request
- `mgid`: Master group ID
- `operator_id`: Owner operator ID
- `nas_id`: Router ID
- `name`: PPPoE username
- `password`: PPPoE password
- `profile`: Associated PPP profile name
- `comment`: Additional metadata (JSON encoded)
- `disabled`: Whether the secret is disabled

**Backup Before Import:**
The system automatically creates a backup of PPP secrets on the router before importing:
```routeros
/ppp/secret/export file=ppp-secret-backup-by-billing{timestamp}
```

**Example:**
```php
// Take backup first
$now = Carbon::now()->timestamp;
$file = 'ppp-secret-backup-by-billing' . $now;
$api->ttyWirte('/ppp/secret/export', ['file' => $file]);

// Import PPP secrets (optional: only enabled users)
if ($customer_import_request->import_disabled_user == 'no') {
    $query = ['disabled' => 'no'];
} else {
    $query = [];
}

$secrets = $api->getMktRows('ppp_secret', $query);

while ($secret = array_shift($secrets)) {
    $mikrotik_ppp_secret = new mikrotik_ppp_secret();
    $mikrotik_ppp_secret->customer_import_request_id = $customer_import_request->id;
    $mikrotik_ppp_secret->mgid = $customer_import_request->mgid;
    $mikrotik_ppp_secret->operator_id = $customer_import_request->operator_id;
    $mikrotik_ppp_secret->nas_id = $customer_import_request->nas_id;
    $mikrotik_ppp_secret->name = $secret['name'];
    $mikrotik_ppp_secret->password = $secret['password'];
    $mikrotik_ppp_secret->profile = $secret['profile'] ?? '';
    $mikrotik_ppp_secret->comment = json_encode($secret['comment'] ?? '', JSON_PARTIAL_OUTPUT_ON_ERROR);
    $mikrotik_ppp_secret->disabled = $secret['disabled'] ?? '';
    $mikrotik_ppp_secret->save();
}
```

**Import Options:**
- `import_disabled_user = 'no'`: Only import enabled users
- `import_disabled_user = 'yes'`: Import all users (enabled and disabled)

#### Import Process Flow

```
Customer Import Request Created
         ↓
MikrotikDbSyncController::sync()
         ↓
    ┌────┴────┐
    ↓         ↓
Delete Old  Connect to
Imports     Router API
    ↓         ↓
    └────┬────┘
         ↓
    Import IP Pools
         ↓
    Import PPP Profiles
         ↓
    Backup PPP Secrets
         ↓
    Import PPP Secrets
         ↓
    Complete
```

### Import Customers from Router (MikMon)

**Command:** `mikmon2radius`
**File:** `app/Console/Commands/Mikmon2RadiusCommand.php`

**Purpose:** Import existing Hotspot customers from MikroTik router to RADIUS

**Usage:**
```bash
php artisan mikmon2radius {router_ip} {user} {password} {port} {operator_id}
```

**Process:**
1. Connects to router via API
2. Reads `/ip hotspot user` entries
3. Filters valid entries (with usage data)
4. Parses comment field: `M/d/Y H:i:s` format for expiry
5. Creates or links packages
6. Creates customer records in RADIUS database
7. Sets MAC address as username/password

**Fields Imported:**
- `name` → Customer name
- `mac-address` → Username and password
- `profile` → Package name
- `comment` → Expiry date (M/d/Y H:i:s)
- `bytes-in` → Used for filtering (must be > 0)

### Import from Excel

**Command:** `import:customers_from_excel`
**File:** `app/Console/Commands/ImportCustomersFromExcelCommand.php`

**Purpose:** Bulk import customers from Excel file

### Master Group Admin Import

**Command:** `master:group_admin_import`
**File:** `app/Console/Commands/MasterGroupAdminImportCommand.php`

**Purpose:** Import complete operator structure with customers

### What Gets Imported

#### MikroTik Resources
- **IP Pools:** Pool names and IP address ranges
- **PPP Profiles:** Profile names, local address, remote address
- **PPP Secrets:** Usernames, passwords, profile assignments, comments, disabled status

#### Customer Data
- Personal information (name, mobile, address)
- Username and password
- Package assignment
- Expiry dates
- Connection type (PPPoE/Hotspot/Static)

#### Linked Data
- **Package:** Links to operator's package
- **Operator:** Associates with owner
- **Billing Profile:** Inherits from operator
- **Parent ID:** Self-reference for renewals

### What Doesn't Get Imported

- Active sessions (these are created as customers connect)
- Usage history (starts fresh)
- Payment records (must be entered separately)
- Router configuration (customers use RADIUS)
- Router-specific settings (firewall rules, NAT, system settings)

---

## 9. Form Fields Reference

### Router Form (`routers.create`)

| Field Name | Form Input | Database Column | Required | Description |
|------------|------------|-----------------|----------|-------------|
| NAS Name | `nasname` | `nasname` | Yes | Router IP address |
| Short Name | `shortname` | `shortname` | Yes | Router identifier |
| Type | `type` | `type` | Yes | Router type (mikrotik) |
| Ports | `ports` | `ports` | No | Service ports |
| Secret | `secret` | `secret` | Yes | RADIUS shared secret (min 16 chars) |
| Server | `server` | `server` | No | Server identifier |
| Community | `community` | `community` | No | SNMP community (default: billing) |
| Description | `description` | `description` | No | Router description |
| API Username | `api_username` | `api_username` | Yes | RouterOS API username |
| API Password | `api_password` | `api_password` | Yes | RouterOS API password |
| API Port | `api_port` | `api_port` | Yes | API port (default: 8728) |

### Router Configuration Form (`routers.configuration.create`)

| Field Name | Form Input | Purpose |
|------------|------------|---------|
| Operator ID | `operator_id` | Operator to configure for |
| Change System Identity | `change_system_identity` | Whether to update router identity |

### Customer Form (PPPoE)

| Field Name | Form Input | Database Column | Required |
|------------|------------|-----------------|----------|
| Name | `name` | `username` | Yes |
| Mobile | `mobile` | `mobile` | Yes |
| Username | `username` | `username` | Yes |
| Password | `password` | `value` (radcheck) | Yes |
| Package | `package_id` | `package_id` | Yes |
| Connection Type | `connection_type` | `connection_type` | Yes |
| Expire Date | `package_expired_at` | `package_expired_at` | Yes |

### Backup Settings Form

| Field Name | Form Input | Database Column | Purpose |
|------------|------------|-----------------|---------|
| NAS ID | `nas_id` | `nas_id` | Primary router for authentication |
| Primary Authenticator | `primary_authenticator` | `primary_authenticator` | Always "Radius" |

---

## 10. Developer Guide

### Architecture Overview

ISP Bills uses a Laravel-based architecture with:
- **Frontend:** Blade templates, Tailwind CSS
- **Backend:** Laravel 9 controllers, models, jobs
- **Database:** MySQL/PostgreSQL with multiple connections
- **Router API:** MikroTik RouterOS API
- **RADIUS:** FreeRADIUS 3.x

### Key Concepts

#### Multi-Tenancy
- **Master Group (mgid):** Top-level organization
- **Group (gid):** Group admin level
- **Operator:** Reseller/ISP operator
- **Sub-operator:** Sub-reseller

#### Database Connections
- **Central:** Main application database
- **Node:** RADIUS database (per operator or shared)

#### Node vs Central
- **Central Mode:** All operators share one RADIUS database
- **Node Mode:** Each operator has separate RADIUS database

### Example: Adding Router via Code

```php
use App\Models\Freeradius\nas;
use Illuminate\Support\Facades\Auth;

// Create new router
$router = new nas();
$router->setConnection(Auth::user()->node_connection);
$router->nasname = '192.168.1.1';
$router->shortname = 'Main-Router';
$router->type = 'mikrotik';
$router->secret = 'strong-secret-key-here';
$router->community = 'billing';
$router->api_username = 'admin';
$router->api_password = 'password';
$router->api_port = 8728;
$router->mgid = Auth::user()->mgid;
$router->gid = Auth::user()->gid;
$router->operator_id = Auth::user()->id;
$router->save();
```

### Example: Configure Router via API

```php
use RouterOS\Sohag\RouterosAPI;
use App\Models\Freeradius\nas;

$router = nas::findOrFail($router_id);

$config = [
    'host' => $router->nasname,
    'user' => $router->api_username,
    'pass' => $router->api_password,
    'port' => $router->api_port,
    'attempts' => 1
];

$api = new RouterosAPI($config);

if ($api->connect($config['host'], $config['user'], $config['pass'])) {
    // Add RADIUS server
    $rows = [
        [
            'address' => '10.0.0.5',
            'authentication-port' => 3612,
            'accounting-port' => 3613,
            'secret' => $router->secret,
            'service' => 'hotspot,ppp',
            'timeout' => '3s',
        ]
    ];
    
    $api->addMktRows('radius', $rows);
    
    // Enable RADIUS
    $api->ttyWirte('/ppp/aaa/set', [
        'use-radius' => 'yes',
        'accounting' => 'yes',
        'interim-update' => '5m'
    ]);
    
    echo "Router configured successfully!";
} else {
    echo "Could not connect to router!";
}
```

### Example: Query Online Customers

```php
use App\Models\Freeradius\customer;
use App\Http\Controllers\QueryInRouterController;

$customer = customer::where('username', 'test-user')->first();

$is_online = QueryInRouterController::getOnlineStatus($customer);

if ($is_online) {
    echo "Customer is online with {$is_online} active session(s)";
} else {
    echo "Customer is offline";
}
```

### Example: Sync Online Customers

```php
use App\Console\Commands\SyncOnlineCustomersWithApiCommand;
use Illuminate\Support\Facades\Artisan;

// Run sync command for operator
Artisan::call('sync:online_customers', ['operator_id' => 1]);

$output = Artisan::output();
echo $output;
```

### Example: Import from Router

```bash
# Import Hotspot customers from MikroTik router
php artisan mikmon2radius 192.168.1.1 admin password 8728 1

# Where:
# - 192.168.1.1: Router IP
# - admin: API username
# - password: API password
# - 8728: API port
# - 1: Operator ID
```

### Router API Methods Reference

**File:** `RouterOS\Sohag\RouterosAPI`

| Method | Purpose | Example |
|--------|---------|---------|
| `connect()` | Connect to router | `$api->connect($host, $user, $pass)` |
| `getMktRows($menu, $filters)` | Get rows from menu | `$api->getMktRows('ppp_active')` |
| `addMktRows($menu, $rows)` | Add rows to menu | `$api->addMktRows('radius', $rows)` |
| `editMktRow($menu, $row, $edit)` | Edit existing row | `$api->editMktRow('ip_hotspot', $row, $edits)` |
| `removeMktRows($menu, $rows)` | Remove rows | `$api->removeMktRows('ppp_secret', $rows)` |
| `ttyWirte($command, $params)` | Execute command * | `$api->ttyWirte('/ppp/aaa/set', ['use-radius' => 'yes'])` |

**Note:** The method name `ttyWirte` is spelled this way in the actual codebase (likely a typo of "Write" during initial development).

### Menu Names for Router API

| Menu Name | RouterOS Path | Purpose |
|-----------|--------------|---------|
| `radius` | `/radius` | RADIUS servers |
| `ppp_profile` | `/ppp profile` | PPP profiles |
| `ppp_secret` | `/ppp secret` | PPPoE users |
| `ppp_active` | `/ppp active` | Active PPPoE sessions |
| `pppoe_server_server` | `/interface pppoe-server server` | PPPoE server settings |
| `ip_hotspot` | `/ip hotspot` | Hotspot servers |
| `ip_hotspot_profile` | `/ip hotspot profile` | Hotspot profiles |
| `hotspot_user_profile` | `/ip hotspot user profile` | Hotspot user profiles |
| `hotspot_user` | `/ip hotspot user` | Hotspot users |
| `hotspot_active` | `/ip hotspot active` | Active hotspot sessions |
| `ip_pool` | `/ip pool` | IP address pools |
| `ip_firewall_nat` | `/ip firewall nat` | NAT rules |
| `ip_firewall_filter` | `/ip firewall filter` | Firewall filter rules |
| `walled_garden_ip` | `/ip hotspot walled-garden ip` | Walled garden IPs |

### Testing Router Configuration

```php
use Tests\Feature\RouterConfigurationTest;

class RouterConfigurationTest extends TestCase
{
    public function test_router_can_be_added()
    {
        $response = $this->actingAs($this->groupAdmin)
            ->post('/routers', [
                'nasname' => '192.168.1.1',
                'shortname' => 'Test-Router',
                'type' => 'mikrotik',
                'secret' => 'test-secret-key-123',
                'api_username' => 'admin',
                'api_password' => 'password',
                'api_port' => 8728,
            ]);
            
        $response->assertStatus(302);
        $this->assertDatabaseHas('nas', [
            'nasname' => '192.168.1.1'
        ]);
    }
}
```

### Common Pitfalls

1. **API Permissions:** Ensure API user has `full` or `write` group membership
2. **Network Access:** RADIUS server must be reachable from router
3. **Firewall Rules:** Port 3612/3613 must be open on RADIUS server
4. **Secret Mismatch:** Router secret must match NAS table secret
5. **Connection Timeout:** Increase timeout if router is slow to respond

### Debugging Tips

```bash
# Test RADIUS authentication
radtest username password localhost:3612 0 testing123

# Check RADIUS logs
tail -f /var/log/freeradius/radius.log

# Test router API
php artisan tinker
>>> $api = new RouterOS\Sohag\RouterosAPI(['host' => '192.168.1.1', 'user' => 'admin', 'pass' => 'password', 'port' => 8728]);
>>> $api->connect('192.168.1.1', 'admin', 'password');
>>> $api->getMktRows('radius');

# Check scheduled tasks
php artisan schedule:list

# Run specific sync command
php artisan sync:online_customers 1 -v
```

---

## Summary

This guide covers the complete onboarding and router configuration process for ISP Bills:

1. **Onboarding** follows a guided workflow ensuring all components are configured
2. **Minimum configuration** requires billing profiles, routers, customers, and settings
3. **Adding routers** involves entering IP, credentials, and RADIUS secret
4. **Configuration** automates setting up MikroTik router for RADIUS integration
5. **Tasks** include status checks, session management, and customer transfers
6. **Scheduler** runs automated tasks every few minutes (sync, accounting, cleanup)
7. **RADIUS** handles authentication, authorization, accounting, and dynamic updates
8. **Import** operations transfer IP pools, PPP profiles, PPP secrets, and customers from routers or Excel files
9. **Forms** use standardized field names mapping to database columns
10. **Developer guide** provides code examples and API references

For additional support, refer to the inline documentation in controller files and the official ISP Bills documentation.

# ✅ Extended Developer Checklist for ISP Bills Refactor

## 1. Environment Setup
- [ ] Install **PHP 8.2+** with required extensions (`mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`).
- [ ] Configure **Composer** to latest stable version.
- [ ] Install **Node.js LTS** and **npm/yarn** for frontend builds.
- [ ] Ensure **Vite 5.x** is installed and integrated with Laravel.
- [ ] Verify **Tailwind CSS 3.x** setup with PostCSS and autoprefixer.

---

## 2. Laravel 12.x Upgrade
- [ ] Update `composer.json` to require `laravel/framework: ^12.0`.
- [ ] Run `composer update` and resolve dependency conflicts.
- [ ] Refactor deprecated helpers (`str_*`, `array_*`) to use `Illuminate\Support\Str` and `Arr`.
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
- [ ] Implement **WebAuthn** for passwordless login.
- [ ] Enforce MAC binding and duplicate session prevention.
- [ ] Test router → RADIUS → Laravel flow for PPPoE and Hotspot.

---

## 5. Billing & Payments
- [ ] Implement daily vs monthly billing cycles.
- [ ] Ensure prepaid/postpaid logic consistency.
- [ ] Validate commission splits across reseller hierarchy.
- [ ] Add SQL constraints to prevent duplicate bills/payments.
- [ ] Test invoice generation (PDF/Excel).
- [ ] **Monthly Billing Customers**: Auto-generate bills on the 1st of each month.
- [ ] **Network Access Termination**: Ensure service is cut off immediately upon package expiry.

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
- [ ] **Customer Notifications**: Send SMS before account expiry.

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
- [ ] Refactor MikroTik API calls into modular services.
- [ ] Move hardcoded IP ranges/firewall rules into config files.
- [ ] Add error handling for router API failures.
- [ ] Validate suspended user blocking via firewall rules.
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
