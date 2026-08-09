# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication and data isolation.

## 🚀 Features

* **Multi-Tenant Architecture**: Each user belongs to a company with isolated data
* **Authentication**: Registration, Login, Logout with Laravel Fortify
* **Company Creation**: New users automatically create their own company
* **Company Owner**: First user becomes the company owner
* **Switch Company**: Scaffold for switching between companies
* **Comprehensive Testing**: 24+ tests with 62 assertions

## 📋 Requirements

* PHP >= 8.2
* Composer
* Node.js & NPM
* SQLite (default) or MySQL

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/jjanahassan/multi-tenant.git
cd multi-tenant
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Set Up Environment

```bash
cp .env.example .env
```

The application is configured to use SQLite by default. The database file will be created at `database/database.sqlite`.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Install Frontend Dependencies

```bash
npm install
npm run build
```

### 6. Create SQLite Database File

```bash
touch database/database.sqlite
```

> **Windows:** If `touch` is not available, create an empty file named `database.sqlite` inside the `database/` directory.

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Start the Development Server

```bash
php artisan serve
```

Visit http://localhost:8000 to see the application.

---

## 🏗️ Architecture

### Database Schema

#### Companies Table

| Column       | Description               |
| ------------ | ------------------------- |
| `id`         | Primary key               |
| `name`       | Company name              |
| `owner_id`   | Foreign key to `users.id` |
| `is_active`  | Soft deactivation status  |
| `created_at` | Creation timestamp        |
| `updated_at` | Last update timestamp     |

#### Users Table

| Column       | Description                   |
| ------------ | ----------------------------- |
| `id`         | Primary key                   |
| `name`       | User's name                   |
| `email`      | Unique email address          |
| `password`   | Hashed password               |
| `company_id` | Foreign key to `companies.id` |
| `created_at` | Creation timestamp            |
| `updated_at` | Last update timestamp         |

### Relationships

* **User belongsTo Company**: Each user belongs to exactly one company.
* **Company hasMany Users**: Each company can have many users.
* **Company hasOne Owner**: One user is the company creator/owner.

---

## 🔐 Registration Flow

When a new user registers:

1. User submits the registration form.
2. A database transaction begins.
3. A company is created with `owner_id = null`.
4. A user is created with `company_id = company.id`.
5. The company is updated with `owner_id = user.id`.
6. The transaction commits.
7. The user is automatically logged in.

Using a transaction ensures that the company and user are created together. If any step fails, the entire operation is rolled back.

---

## 🧪 Testing

Run the complete test suite with:

```bash
php artisan test
```

### Test Coverage

```text
Tests: 24 passed (62 assertions)
Duration: ~2s
```

### Test Types

* **Registration Tests**

  * User registration
  * Company creation
  * Company isolation

* **Authentication Tests**

  * Login
  * Logout
  * Invalid credentials

* **Dashboard Tests**

  * Access control
  * Guest redirection

* **Profile Tests**

  * Profile updates
  * Password changes
  * Account deletion

* **Password Reset Tests**

  * Reset link
  * Token validation
  * Password update

---

## 📁 Project Structure

```text
multi-tenant/
├── app/
│   ├── Actions/
│   │   └── Fortify/
│   │       └── CreateNewUser.php      # Registration logic
│   ├── Models/
│   │   ├── Company.php                # Company model
│   │   └── User.php                   # User model with company relationship
│   └── Traits/
│       └── CompanyScoped.php          # Data isolation trait
│
├── database/
│   └── migrations/
│       ├── create_companies_table.php
│       └── add_company_id_to_users_table.php
│
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── switch-company.blade.php
│       └── layouts/
│           ├── app.blade.php
│           ├── guest.blade.php
│           └── navigation.blade.php
│
├── routes/
│   └── web.php
│
├── tests/
│   └── Feature/
│       └── Auth/
│           ├── RegistrationTest.php
│           └── LoginTest.php
│
└── README.md
```

---

## 🔄 Branching Strategy

The project follows a feature-branch workflow:

* `master` — Production-ready code
* `feature/*` — Feature branches (e.g., `feature/task1`)

Each feature branch is merged into `master` via Pull Request.

---

# 📝 Task 1: Multi-Tenant Data Model & Auth

## Acceptance Criteria

* ✅ Two separate people can register with isolated companies
* ✅ Schema decisions are documented
* ✅ Basic tests exist for registration and login

## Schema Decisions

### Why single company per user?

The initial implementation uses a single-company relationship for each user because:

* It simplifies the initial implementation.
* Each user has isolated data.
* The architecture can be extended to support multiple companies later.

### Why `owner_id`?

The `owner_id` field:

* Tracks the company creator.
* Identifies the company owner.
* Enables future admin/owner privileges.

### Why `is_active`?

The `is_active` field allows a company to be deactivated without deleting its data.

This provides a soft-deactivation mechanism and preserves historical information.

### Why nullable foreign keys?

The `owner_id` field is nullable to handle the circular dependency during registration:

```text
Company needs a User
User needs a Company
```

The registration process therefore works in three stages:

```text
1. Create Company with owner_id = null
2. Create User with company_id = company.id
3. Update Company with owner_id = user.id
```

The entire process is wrapped in a database transaction to maintain data consistency.

---

## 📄 License

This project is open-sourced software licensed under the MIT License.

---

## 👤 Author

**Jana Hassan**

* GitHub: [@jjanahassan](https://github.com/jjanahassan)
* Email: `janahassan210@yahoo.com`
