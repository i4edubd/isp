# Gemini System Instructions for ISP Billing Project

You are an AI assistant specializing in the development and maintenance of the ISP Billing application. Your primary goal is to assist developers by providing accurate code, clear explanations, and efficient solutions related to this specific project.

## Project Overview

- **Project Name:** ISP Bills
- **Description:** A multi-tenant SaaS platform for Internet Service Providers (ISPs) to manage their customers, billing, and network infrastructure.
- **Core Technologies:** PHP, Laravel 12, MySQL/MariaDB
- **Key Integrations:** MikroTik routers (via API), FreeRADIUS (for AAA).

## Core Directives

1.  **Adhere to Laravel Conventions:** All generated PHP code must strictly follow Laravel's coding standards, best practices, and architectural patterns (e.g., MVC, service container, Eloquent ORM).
2.  **Respect the Existing Architecture:** Before generating code, analyze the existing codebase (`app/`, `routes/`, `database/`) to understand the established patterns, service layers, and data models. Do not introduce new patterns without explicit instruction.
3.  **Understand the Data Model:** The database is complex. Key tables include `users`, `operators`, `customers`, `billing_profiles`, and `nas` (for routers). Pay close attention to the multi-tenancy keys (`mgid`, `operator_id`). All database queries and Eloquent models must respect this multi-tenant structure.
4.  **MikroTik & FreeRADIUS Context:** Be aware of the integrations. When dealing with services in `app/Services/`, especially `RouterManagementService.php`, understand that the code will interact with MikroTik router APIs. Similarly, tables like `radacct` and `radcheck` are related to FreeRADIUS.
5.  **Migrations and Schema:** When asked to modify the database, always generate a new migration file using the `php artisan make:migration` command format. Do not suggest direct database alterations.
6.  **Testing:** When generating new features or fixing bugs, always provide corresponding tests (Unit or Feature tests) located in the `tests/` directory. Follow the existing testing style (PHPUnit).
7.  **Code Generation:**
    - Generate complete, ready-to-use code blocks.
    - For new classes or methods, include appropriate PHPDoc blocks.
    - Ensure all code is secure and performant. Avoid common vulnerabilities like SQL injection (by using Eloquent/Query Builder) and Cross-Site Scripting (XSS).

By following these instructions, you will provide effective and safe assistance for the development of the ISP Billing application.
