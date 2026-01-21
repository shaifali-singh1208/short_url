# URL Shortener Service

A multi-tenant URL shortener service built with Laravel.

## Features

- **Role-Based Access Control**: SuperAdmin, Admin, Member, Sales, Manager.
- **Multi-Tenancy**: Users belong to companies; visibility rules depend on company context.
- **Invitation System**: Role-specific invitation restrictions.
- **URL Shortener**: Restricted creation and visibility rules.
- **Redirection**: Authenticated-only short URL redirection.

## Prerequisites

- PHP 8.2+
- Composer
- SQLite (default) or MySQL

## Setup Instructions

1. **Clone the repository**:

    ```bash
    git clone <repo-url>
    cd url-shortener
    ```

2. **Install dependencies**:

    ```bash
    composer install
    npm install
    npm run build
    ```

3. **Environment Setup**:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **Database Configuration**:
   The project is configured to use SQLite by default. Ensure a database file exists:

    ```bash
    touch database/database.sqlite
    ```

    (Or configure MySQL in `.env`)

5. **Run Migrations and Seeders**:

    ```bash
    php artisan migrate:fresh
    php artisan db:seed --class=SuperAdminSeeder
    ```

6. **Serve the Application**:
    ```bash
    php artisan serve
    ```

## Test Accounts

- **SuperAdmin**: `admin@gmail.com` / `123456`

## Running Tests

To verify the business requirements:

```bash
php artisan test tests/Feature/UrlShortenerTest.php
```

## AI Usage Disclosure

- **Antigravity (Google DeepMind)**: Used for architecting the solution, generating boilerplate, implementing complex business logic for visibility/invitations, and refining the UI based on the provided Figma mockup.
- **Laravel Documentation**: Referenced for Eloquent relationship syntax and validation rules.
