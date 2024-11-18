# Construction LMS

A Learning Management System (LMS) built specifically for the construction industry using Laravel and modern web technologies.

## Overview

Construction LMS is a web-based platform that enables construction businesses and individuals to manage, deliver, and track learning content and certifications. The system supports multiple user roles and can handle both independent users and business-associated learners.

## Features

- **Multi-Role System**
  - User Role (can be business-associated or independent)
  - Business Role (manages business-specific content and users)
  - Admin Role (full system management)

- **Course Management**
  - Course creation and delivery
  - Progress tracking
  - Certificate generation
  - Business-specific course management

- **Business Features**
  - Employee management
  - Business-specific reporting
  - Certificate tracking
  - Course management for business users

- **Payment Integration**
  - Stripe payment processing
  - Subscription management
  - Course purchasing system

## Technology Stack

- **Backend:** Laravel 11
- **Frontend:** 
  - Tailwind CSS
  - Wire Elements Modal
- **Database:** MySQL/PostgreSQL
- **Authentication:** Laravel Sanctum
- **PDF Generation:** Laravel DomPDF
- **Payment Processing:** Laravel Cashier (Stripe)
- **Authorization:** Spatie Laravel Permission

## Requirements

- PHP ^8.2
- Node.js
- Composer
- MySQL/PostgreSQL
- Stripe Account (for payments)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Configure your environment variables in `.env`:
- Database connection
- Stripe keys
- Mail settings
- Application URL

6. Generate application key:
```bash
php artisan key:generate
```

7. Run database migrations:
```bash
php artisan migrate
```

8. Build assets:
```bash
npm run build
```

## Development

For local development:

```bash
php artisan serve
npm run dev
```

## Testing

Run the test suite:

```bash
php artisan test
```

## License

This project is licensed under the MIT License.
