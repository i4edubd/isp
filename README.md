
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

## 🛠️ Development Notes
- Backend: Laravel 12.x (PHP 8.2+)
- Frontend: Tailwind CSS 3.x, Vite 5.x, Node.js LTS
- Router Integration: MikroTik API
- Authentication: FreeRADIUS + WebAuthn

For setup instructions and developer checklists, see **ARCHITECTURE.md**.


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




# Metronic Tailwind HTML Laravel Integration

This project integrates Metronic Tailwind HTML themes into a Laravel application, providing 10 complete demo layouts showcasing different UI patterns and design approaches.

## Project Overview

**Goal**: Convert Metronic Tailwind HTML demo layouts (Demo1 through Demo10) into standard Laravel Blade views, providing a comprehensive showcase of Metronic's design system within Laravel's MVC architecture.

## Tech Stack

- **Laravel**: 12.x (Latest)
- **PHP**: 8.2+
- **Tailwind CSS**: 3.x
- **Vite**: 5.x for asset building
- **Node.js**: Latest LTS version

## Project Structure

```
app/Http/Controllers/
├── Demo1Controller.php
├── Demo2Controller.php
├── ...
└── Demo10Controller.php

resources/views/
├── layouts/
│   ├── partials/
│   │   ├── head.blade.php
│   │   └── scripts.blade.php
│   ├── demo1/
│   │   ├── base.blade.php
│   │   └── partials/
│   ├── demo2/
│   │   ├── base.blade.php
│   │   └── partials/
│   └── ... (demo3-demo10)
├── pages/
│   ├── demo1/
│   │   └── index.blade.php
│   ├── demo2/
│   │   └── index.blade.php
│   └── ... (demo3-demo10)
└── components/
    ├── demo1/
    ├── demo2/
    ├── ... (demo3-demo10)
    └── shared/

public/assets/
├── css/
│   └── styles.css
├── js/
│   ├── core.bundle.js
│   └── layouts/
│       ├── demo1.js
│       ├── demo2.js
│       └── ... (demo3-demo10.js)
├── media/
└── vendors/
```

## Demo Layouts

This integration includes 10 complete demo layouts, each showcasing different UI patterns:

- **Demo 1**: Sidebar Layout - Traditional admin dashboard with sidebar navigation
- **Demo 2**: Header Layout - Modern dashboard with top navigation
- **Demo 3**: Minimal Layout - Clean, minimalist design approach
- **Demo 4**: Creative Layout - Creative and artistic dashboard design
- **Demo 5**: Modern Layout - Contemporary UI with modern elements
- **Demo 6**: Professional Layout - Business-focused professional design
- **Demo 7**: Corporate Layout - Enterprise-grade corporate dashboard
- **Demo 8**: Executive Layout - Executive-level dashboard interface
- **Demo 9**: Premium Layout - Premium design with advanced components
- **Demo 10**: Ultimate Layout - Most comprehensive layout with all features

## Features

### ✅ Core Implementation

1. **Laravel MVC Architecture**
   - Dedicated controllers for each demo (Demo1Controller - Demo10Controller)
   - Clean routing structure with named routes
   - Blade template inheritance and components

2. **Asset Management**
   - Metronic CSS and JavaScript assets properly integrated
   - Laravel asset helpers for proper path resolution
   - Vite integration for development workflow

3. **Template System**
   - Blade layouts for each demo with proper inheritance
   - Reusable partials for headers, sidebars, and footers
   - Component-based architecture for UI elements

4. **Responsive Design**
   - Mobile-first responsive layouts
   - Touch-friendly navigation
   - Adaptive components across all screen sizes

### 🎨 Design System

- **Metronic Tailwind CSS** - Complete design system integration
- **Theme Support** - Light and dark mode switching
- **Custom Components** - Metronic-specific UI components
- **Icon System** - Comprehensive icon library integration

## Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js (LTS version)
- A web server (Apache/Nginx) or use Laravel's built-in server

### Installation

1. **Clone the repository**
```bash
git clone https://github.com/keenthemes/metronic-tailwind-html-integration.git
cd metronic-tailwind-html-integration/metronic-tailwind-laravel
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node.js dependencies**
```bash
npm install
```

4. **Copy Metronic assets**
```bash
# Copy assets from metronic-tailwind-html/dist/assets to public/assets/
cp -r ../metronic-tailwind-html/dist/assets public/
```

5. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

6. **Start development servers**
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Available Routes
- **Demo 1**: `/demo1` - Sidebar Layout
- **Demo 2**: `/demo2` - Header Layout
- **Demo 3**: `/demo3` - Minimal Layout
- **Demo 4**: `/demo4` - Creative Layout
- **Demo 5**: `/demo5` - Modern Layout
- **Demo 6**: `/demo6` - Professional Layout
- **Demo 7**: `/demo7` - Corporate Layout
- **Demo 8**: `/demo8` - Executive Layout
- **Demo 9**: `/demo9` - Premium Layout
- **Demo 10**: `/demo10` - Ultimate Layout

## Production Deployment

### Build for Production
```bash
# Build optimized assets
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
composer install --optimize-autoloader --no-dev
```

## Customization

### Adding Your Own Content
1. **Controllers**: Modify demo controllers to return your actual data
2. **Views**: Customize Blade templates with your content
3. **Components**: Create new Blade components for your specific needs
4. **Styling**: Add custom CSS in `resources/css/app.css`

### Extending Layouts
- Each demo layout is independent and can be customized separately
- Shared partials allow for consistent elements across demos
- Component system enables reusable UI elements

## Architecture

### Design Principles
- **MVC Pattern**: Clean separation using Laravel's MVC architecture
- **Component-Based**: Reusable Blade components for UI elements
- **Asset Integration**: Proper integration of Metronic assets with Laravel
- **Responsive Design**: Mobile-first approach across all layouts

### File Organization
- **Controllers**: One controller per demo layout
- **Views**: Organized by demo with shared layouts and partials
- **Assets**: Metronic assets properly integrated in `public/assets/`
- **Components**: Reusable UI components for consistent functionality

## Documentation

For detailed integration steps and customization guides, refer to the complete documentation in the main repository.

## Support

For questions and support:
- Review the integration documentation
- Check the demo implementations for examples
- Refer to Laravel documentation for framework-specific questions
