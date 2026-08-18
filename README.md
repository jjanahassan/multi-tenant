# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, and tenant data isolation.

## 🚀 Features

* **Multi-Tenant Architecture**: Each user belongs to a company with isolated data.
* **Authentication**: Registration, Login, Logout with Laravel Fortify.
* **Company Creation**: New users automatically create their own company.
* **Company Owner**: The first registered user becomes the company owner.
* **Role-Based Authorization**: Supports `owner`, `admin`, and `member` roles.
* **Tenant Data Isolation**: Users can only access data belonging to their own company.
* **Policy-Based Authorization**: Company actions are protected using Laravel Policies.
* **Invite Teammate**: Owners and admins can invite teammates with a specified role.
* **Queued Invitation Job**: Invitations dispatch a placeholder queued job for future email delivery.
* **Switch Company**: Scaffold for switching between companies.
* **Comprehensive Testing**: 43 passing tests with 95 assertions.

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

The application is configured to use SQLite by default. The database file is located at:

```text
database/database.sqlite
```

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

**Windows:** If `touch` is not available, create an empty file named `database.sqlite` inside the `database/` directory.

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Start the Development Server

```bash
php artisan serve
```

Visit:

```text
http://localhost:8000
```

## 🏗️ Architecture

### Database Schema

#### Companies Table

| Column       | Description                        |
| ------------ | ---------------------------------- |
| `id`         | Primary key                        |
| `name`       | Company name                       |
| `owner_id`   | Foreign key to the company's owner |
| `is_active`  | Company active/deactivation status |
| `created_at` | Creation timestamp                 |
| `updated_at` | Last update timestamp              |

#### Users Table

| Column       | Description                          |
| ------------ | ------------------------------------ |
| `id`         | Primary key                          |
| `name`       | User's name                          |
| `email`      | Unique email address                 |
| `password`   | Hashed password                      |
| `company_id` | Foreign key to the user's company    |
| `role`       | User's role: owner, admin, or member |
| `created_at` | Creation timestamp                   |
| `updated_at` | Last update timestamp                |

#### Invitations Table

| Column       | Description                           |
| ------------ | ------------------------------------- |
| `id`         | Primary key                           |
| `company_id` | Company sending the invitation        |
| `email`      | Email address of the invited teammate |
| `role`       | Role assigned to the invited teammate |
| `token`      | Unique invitation token               |
| `expires_at` | Invitation expiration timestamp       |
| `created_at` | Creation timestamp                    |
| `updated_at` | Last update timestamp                 |

### Relationships

* **User belongsTo Company**: Each user belongs to exactly one company through `company_id`.
* **Company hasMany Users**: A company can have multiple users.
* **Company belongsTo Owner**: The company stores the owner's user ID in `owner_id`.

---

# 🔐 Task 1: Multi-Tenant Data Model & Authentication

## Goal

Create the foundation of the multi-tenant application by connecting users to companies and implementing authentication.

## Registration Flow

When a new user registers:

1. The user submits the registration form.
2. A database transaction begins.
3. A company is created with `owner_id = null`.
4. A user is created with `company_id = company.id`.
5. The user's role is set to `owner`.
6. The company's `owner_id` is updated to the new user's ID.
7. The transaction commits.
8. The user is automatically logged in.

The registration process is wrapped in a database transaction so that if any part fails, the company and user creation are rolled back together.

```text
New User
   │
   ├── creates Company
   │
   ├── User.company_id → Company.id
   │
   ├── User.role → owner
   │
   └── Company.owner_id → User.id
```

## Task 1 Acceptance Criteria

* ✅ Two separate people can register.
* ✅ Each user receives their own company.
* ✅ Users are isolated by company.
* ✅ The first user becomes the company owner.
* ✅ Registration and authentication are covered by tests.
* ✅ Schema decisions are documented.

## Task 1 Schema Decisions

### Why single company per user?

The initial implementation uses a single-company relationship for each user because:

* It simplifies the initial implementation.
* Each user has isolated data.
* It is easy to understand and maintain.
* The architecture can be extended to support multiple companies later.

### Why `owner_id`?

The `owner_id` field:

* Tracks the company creator.
* Identifies the company owner.
* Enables owner-specific permissions.

### Why `is_active`?

The `is_active` field allows a company to be deactivated without deleting its data.

This provides a soft-deactivation mechanism and preserves historical information.

### Why nullable `owner_id`?

The `owner_id` field is nullable to handle the circular dependency during registration:

```text
Company needs a User
User needs a Company
```

The registration process therefore works in three stages:

1. Create Company with `owner_id = null`
2. Create User with `company_id = company.id`
3. Update Company with `owner_id = user.id`

The entire process is wrapped in a database transaction to maintain data consistency.

---

# 🔐 Task 2: Roles, Permissions & Tenant Isolation

## Goal

Ensure that users can only see and act on data belonging to their own company, while permissions differ according to their role.

Task 2 introduces:

* `owner`, `admin`, and `member` roles.
* Tenant data isolation.
* Policy-based authorization.
* Teammate invitations.
* A queued placeholder job for invitation emails.

## Roles

The application stores roles directly in a `role` column on the users table.

The available roles are:

* `owner`
* `admin`
* `member`

### Why use a role column?

A role column was chosen instead of introducing a separate roles table or third-party permissions package because the application currently only requires three clearly defined roles.

This approach:

* Keeps the implementation simple.
* Avoids unnecessary dependencies.
* Makes role checks easy to understand.
* Is sufficient for the current requirements.
* Can be extended into a more advanced permission system later if needed.

## Role Permissions

| Action          | Owner | Admin | Member |
| --------------- | :---: | :---: | :----: |
| Invite teammate |   ✅   |   ✅   |    ❌   |
| Remove teammate |   ✅   |   ✅   |    ❌   |
| Delete company  |   ✅   |   ❌   |    ❌   |

The owner has full company-management permissions.

Admins can manage teammates but cannot delete the company.

Members cannot perform company-management actions.

---

## Tenant Isolation

A user from one company must never be able to access data belonging to another company.

The application uses the `CompanyScoped` trait to automatically restrict company-owned data to the authenticated user's company.

The basic flow is:

```text
Authenticated User
        │
        ▼
    company_id
        │
        ▼
CompanyScoped Query
        │
        ▼
Only records belonging to that company
```

For example, if a user belongs to Company A, company-scoped queries automatically exclude records belonging to Company B.

### Why use a global query scope?

A global query scope was chosen because it provides a centralized way to enforce tenant isolation.

Without a scope, developers would have to remember to manually add:

```php
->where('company_id', auth()->user()->company_id)
```

to every relevant query.

The scope reduces the possibility of accidentally exposing another company's data.

Tenant isolation is also enforced when creating records by automatically associating new records with the authenticated user's company.

### Tenant Isolation Rules

A user must not be able to:

* Query another company's data.
* View another company's data.
* Edit another company's data.
* Create records under another company.

These rules are verified through automated feature tests.

---

## Authorization Policies

Company-management permissions are enforced using Laravel Policies.

The main authorization logic is contained in:

```text
app/Policies/CompanyPolicy.php
```

### Why use Policies?

Policies were chosen instead of relying only on the frontend or hiding buttons because UI restrictions are not security mechanisms.

For example, even if a member does not see an "Invite Teammate" button, they could manually send a request to the invitation endpoint.

The policy prevents that request from succeeding.

The authorization flow is:

```text
User Request
     │
     ▼
Controller
     │
     ▼
CompanyPolicy
     │
     ├── Authorized → Continue
     │
     └── Unauthorized → 403 Forbidden
```

### Invite Teammate

Only users with the `owner` or `admin` role can invite teammates.

* Owner → allowed
* Admin → allowed
* Member → denied

### Remove Teammate

Only users with the `owner` or `admin` role can remove teammates.

* Owner → allowed
* Admin → allowed
* Member → denied

### Delete Company

Only the company owner can delete the company.

* Owner → allowed
* Admin → denied
* Member → denied

The policy also verifies that the authenticated user belongs to the company they are attempting to manage.

This prevents a user from Company A from performing company-management actions against Company B.

---

# 📧 Invite Teammate

Owners and admins can invite new teammates.

The invitation form accepts:

* Email address.
* Role.

Only these roles can be assigned through an invitation:

* `admin`
* `member`

An owner cannot be invited because each company has one owner, who is created during company registration.

## Invitation Process

```text
Owner/Admin
     │
     ▼
Invite Teammate
     │
     ├── Email
     └── Role
     │
     ▼
Authorization Policy
     │
     ▼
Validation
     │
     ▼
Create Invitation
     │
     ├── company_id
     ├── email
     ├── role
     ├── token
     └── expires_at
     │
     ▼
Dispatch SendTeamInvitation Job
```

The application currently uses a queued placeholder job for the invitation email.

The actual email delivery can be implemented later without changing the invitation creation flow.

## Invitation Security

Every invitation:

* Belongs to the authenticated user's company.
* Contains a unique token.
* Has an expiration date.
* Can only assign `admin` or `member`.
* Cannot assign the `owner` role.

The `company_id` is taken from the authenticated user's company rather than from user input.

This prevents a user from creating an invitation belonging to another company.

---

# 🧪 Testing

Run the complete test suite with:

```bash
php artisan test
```

The current test suite contains:

* **43 passed**
* **95 assertions**

There is also one skipped and one risky test related to existing Laravel authentication/2FA functionality.

## Task 1 Tests

### Registration Tests

* Registration screen can be rendered.
* New users can register.
* New users are assigned to their own company.
* The first user becomes the company owner.
* Different users are assigned to different companies.

### Authentication Tests

* Login screen can be rendered.
* Users can authenticate.
* Invalid credentials are rejected.
* Users can log out.

### Dashboard Tests

* Guests are redirected to login.
* Authenticated users can access the dashboard.

## Task 2 Authorization Tests

### Role Authorization Tests

The application tests all required role combinations:

* Owner can invite a teammate.
* Owner can remove a teammate.
* Owner can delete the company.
* Admin can invite a teammate.
* Admin can remove a teammate.
* Admin cannot delete the company.
* Member cannot invite a teammate.
* Member cannot remove a teammate.
* Member cannot delete the company.
* Users cannot perform company actions against another company.

### Tenant Isolation Tests

The application verifies that:

* Users can only see projects belonging to their company.
* Users cannot edit projects belonging to another company.
* New projects are automatically assigned to the authenticated user's company.
* A user from Company A cannot query data belonging to Company B.

The cross-tenant query test directly verifies the main Task 2 tenant-isolation acceptance criterion.

### Invitation Tests

The invitation flow is tested to verify that:

* Owner can create an invitation.
* Admin can create an invitation.
* Member cannot create an invitation.
* An invitation cannot assign the owner role.
* Invitations always belong to the authenticated user's company.

---

# ✅ Task 2 Acceptance Criteria

All Task 2 acceptance criteria are satisfied:

* ✅ A user from Company A cannot query, view, or edit data from Company B.
* ✅ Cross-tenant access is verified through automated tests.
* ✅ Role restrictions are enforced by Laravel Policies.
* ✅ Owner permissions are enforced.
* ✅ Admin permissions are enforced.
* ✅ Member restrictions are enforced.
* ✅ At least three authorization tests exist covering allowed, denied-by-role, and denied-by-tenant scenarios.
* ✅ Teammate invitations include an email and role.
* ✅ Invitation creation is restricted to authorized roles.
* ✅ Invitation creation is scoped to the authenticated user's company.
* ✅ A queued placeholder job is dispatched for invitation delivery.
* ✅ The invitation functionality is accessible through the navigation for authorized users.

---

# 📁 Project Structure

```text
multi-tenant/
├── app/
│   ├── Actions/
│   │   └── Fortify/
│   │       └── CreateNewUser.php
│   │
│   ├── Http/
│   │   └── Controllers/
│   │       └── InvitationController.php
│   │
│   ├── Jobs/
│   │   └── SendTeamInvitation.php
│   │
│   ├── Models/
│   │   ├── Company.php
│   │   ├── Invitation.php
│   │   ├── Project.php
│   │   └── User.php
│   │
│   ├── Policies/
│   │   └── CompanyPolicy.php
│   │
│   └── Traits/
│       └── CompanyScoped.php
│
├── database/
│   └── migrations/
│       ├── create_companies_table.php
│       ├── add_company_id_to_users_table.php
│       ├── add_role_to_users_table.php
│       ├── create_projects_table.php
│       └── create_invitations_table.php
│
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── switch-company.blade.php
│       ├── invitations/
│       │   └── create.blade.php
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
│       ├── Auth/
│       │   ├── RegistrationTest.php
│       │   └── LoginTest.php
│       │
│       └── Authorization/
│           ├── RoleAuthorizationTest.php
│           ├── TenantIsolationTest.php
│           └── InvitationTest.php
│
└── README.md
```

---

# 🔄 Branching Strategy

The project follows a feature-branch workflow:

* `main` — Production-ready code.
* `feature/*` — Feature branches for individual tasks.

Each feature branch is completed, tested, and merged into `main` through a Pull Request or local Git merge.

---

# 📄 License

This project is open-sourced software licensed under the MIT License.

---

# 👤 Author

**Jana Hassan**

GitHub: [@jjanahassan](https://github.com/jjanahassan)

Email: [janahassan210@yahoo.com](mailto:janahassan210@yahoo.com)
