# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, tenant data isolation, and project management with customizable Kanban boards.

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
* **Projects**: Companies can create, view, edit, and delete their own projects.
* **Customizable Kanban Boards**: Each project can have configurable board columns.
* **Board Column Management**: Board columns can be added, renamed, deleted, and reordered.
* **Request Validation**: Form Requests validate project and board column create and update operations.
* **Tenant-Scoped Projects**: Projects are automatically restricted to the authenticated user's company.
* **Switch Company**: Scaffold for switching between companies.
* **Comprehensive Testing**: 52 passing tests with 117 assertions.

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

| Column | Description |
| --- | --- |
| `id` | Primary key |
| `name` | Company name |
| `owner_id` | Foreign key to the company's owner |
| `is_active` | Company active/deactivation status |
| `created_at` | Creation timestamp |
| `updated_at` | Last update timestamp |

#### Users Table

| Column | Description |
| --- | --- |
| `id` | Primary key |
| `name` | User's name |
| `email` | Unique email address |
| `password` | Hashed password |
| `company_id` | Foreign key to the user's company |
| `role` | User's role: owner, admin, or member |
| `created_at` | Creation timestamp |
| `updated_at` | Last update timestamp |

#### Invitations Table

| Column | Description |
| --- | --- |
| `id` | Primary key |
| `company_id` | Company sending the invitation |
| `email` | Email address of the invited teammate |
| `role` | Role assigned to the invited teammate |
| `token` | Unique invitation token |
| `expires_at` | Invitation expiration timestamp |
| `created_at` | Creation timestamp |
| `updated_at` | Last update timestamp |

#### Projects Table

| Column | Description |
| --- | --- |
| `id` | Primary key |
| `company_id` | Foreign key to the project owner company |
| `name` | Project name |
| `description` | Optional project description |
| `created_at` | Creation timestamp |
| `updated_at` | Last update timestamp |

#### Board Columns Table

| Column | Description |
| --- | --- |
| `id` | Primary key |
| `project_id` | Foreign key to the project |
| `name` | Board column name |
| `position` | Numeric position used to determine column order |
| `created_at` | Creation timestamp |
| `updated_at` | Last update timestamp |

### Relationships

* **User belongsTo Company**: Each user belongs to exactly one company through `company_id`.
* **Company hasMany Users**: A company can have multiple users.
* **Company belongsTo Owner**: The company stores the owner's user ID in `owner_id`.
* **Project belongsTo Company**: Each project belongs to one company.
* **Company hasMany Projects**: A company can own multiple projects.
* **BoardColumn belongsTo Project**: Each board column belongs to one project.
* **Project hasMany BoardColumns**: Each project can contain multiple board columns.

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

| Action | Owner | Admin | Member |
| --- | :---: | :---: | :---: |
| Invite teammate | ✅ | ✅ | ❌ |
| Remove teammate | ✅ | ✅ | ❌ |
| Delete company | ✅ | ❌ | ❌ |

The owner has full company-management permissions.

Admins can manage teammates but cannot delete the company.

Members cannot perform company-management actions.

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

# 📋 Task 3: Projects & Boards CRUD

## Goal

Allow each company to organize its work into projects, with each project containing a customizable Kanban board.

Task 3 introduces:

* Projects belonging to companies.
* Full project CRUD functionality.
* Customizable board columns for each project.
* Board column creation, renaming, deletion, and reordering.
* Form Request validation for create and update operations.
* Feature tests covering CRUD operations and validation failures.

## Projects

Each project belongs to a company through the `company_id` foreign key.

The project contains:

* A required name.
* An optional description.
* The company it belongs to.

Projects are automatically scoped to the authenticated user's company using the existing `CompanyScoped` trait.

This means that when a user queries projects, projects belonging to other companies are automatically excluded.

### Why scope projects by company?

Projects contain company-specific work and must not be shared between tenants.

Using the existing tenant isolation mechanism ensures that:

* Users only see projects belonging to their company.
* Users cannot access projects from another company through URLs.
* Users cannot edit projects from another company.
* Users cannot delete projects from another company.
* New projects are automatically associated with the authenticated user's company.

## Project CRUD

The application provides full CRUD functionality for projects.

Users can:

* View all projects belonging to their company.
* Create a new project.
* View an individual project and its board.
* Edit a project's name or description.
* Delete a project.

The project routes are handled through Laravel's resource routing:

```php
Route::resource('projects', ProjectController::class);
```

The main project flow is:

```text
Projects List
     │
     ├── Create Project
     │
     ├── View Project
     │       │
     │       └── Manage Board Columns
     │
     ├── Edit Project
     │
     └── Delete Project
```

# 🗂️ Kanban Board Columns

Each project has its own set of board columns.

Examples include:

* To Do
* In Progress
* Done

However, these are not fixed values. Each project can have its own customized columns.

Users can:

* Add a new column.
* Rename an existing column.
* Delete a column.
* Move a column left.
* Move a column right.

## Column Ordering

Board columns contain a `position` field.

The position value determines the order in which columns are displayed.

For example:

```text
Position 1 → To Do
Position 2 → In Progress
Position 3 → Done
```

When a user moves a column left or right, the application swaps its position value with the adjacent column.

The board columns are always retrieved in ascending order of their position.

```php
->orderBy('position')
```

### Why use a position column?

A numeric `position` column was chosen because it provides a simple and predictable way to maintain the order of board columns.

It also makes the reordering logic straightforward:

```text
Current Column
      │
      ├── Move Left → Swap with previous column
      │
      └── Move Right → Swap with next column
```

This approach avoids introducing unnecessary complexity while still allowing the board to be fully configurable.

## Board Column Security

A board column must belong to the project it is being accessed through.

Before updating, deleting, or reordering a board column, the application verifies that:

```php
$boardColumn->project_id === $project->id
```

If the board column does not belong to the specified project, the request is rejected.

This prevents a board column from one project from being manipulated through another project's URL.

---

# 📝 Form Request Validation

Task 3 uses Laravel Form Requests for create and update operations.

Form Requests separate validation logic from controllers and keep the controller methods focused on application behavior.

The application includes validation for:

* Creating projects.
* Updating projects.
* Creating board columns.
* Updating board columns.

Validation errors are automatically returned to the user and displayed in the interface rather than causing raw exceptions.

## Why use Form Requests?

Form Requests were chosen because they:

* Keep validation logic separate from controllers.
* Make controller methods easier to read.
* Allow validation rules to be reused and maintained independently.
* Use Laravel's standard validation error handling.

The flow is:

```text
User Submission
       │
       ▼
Form Request Validation
       │
       ├── Invalid → Return with validation errors
       │
       └── Valid
             │
             ▼
        Controller
             │
             ▼
        Create/Update Record
```

---

# 🧪 Testing

Run the complete test suite with:

```bash
php artisan test
```

The current test suite contains:

* **52 passed**
* **117 assertions**

There is also:

* **1 skipped test**
* **1 risky test**

The skipped and risky tests are related to existing Laravel authentication/2FA functionality.

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

The cross-tenant query test directly verifies the main tenant-isolation acceptance criterion.

### Invitation Tests

The invitation flow is tested to verify that:

* Owner can create an invitation.
* Admin can create an invitation.
* Member cannot create an invitation.
* An invitation cannot assign the owner role.
* Invitations always belong to the authenticated user's company.

## Task 3 Project and Board Tests

Task 3 includes feature tests covering the main project and board functionality.

The tests cover:

* Creating a project.
* Updating a project.
* Deleting a project.
* Project validation failures.
* Creating board columns.
* Updating or renaming board columns.
* Deleting board columns.
* Reordering board columns.
* Ensuring board columns belong to the correct project.

Factories are also configured for the main models used in testing, allowing test data to be created with the correct company and project relationships.

---

# ✅ Task 3 Acceptance Criteria

All Task 3 acceptance criteria are satisfied:

* ✅ A project can be created through the UI.
* ✅ A project can be edited through the UI.
* ✅ A project can be deleted through the UI.
* ✅ Board columns can be created.
* ✅ Board columns can be renamed.
* ✅ Board columns can be reordered.
* ✅ Board columns can be deleted.
* ✅ Projects are scoped to the authenticated user's company.
* ✅ Board columns belong to their respective projects.
* ✅ Form Requests validate create and update operations.
* ✅ Validation errors are displayed to the user.
* ✅ Feature tests cover project creation, updating, and deletion.
* ✅ At least one validation failure is covered by automated tests.
* ✅ The complete test suite passes with 52 passing tests and 117 assertions.

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
│   │   ├── Controllers/
│   │   │   ├── InvitationController.php
│   │   │   ├── ProjectController.php
│   │   │   └── BoardColumnController.php
│   │   │
│   │   └── Requests/
│   │       ├── StoreProjectRequest.php
│   │       ├── UpdateProjectRequest.php
│   │       ├── StoreBoardColumnRequest.php
│   │       └── UpdateBoardColumnRequest.php
│   │
│   ├── Jobs/
│   │   └── SendTeamInvitation.php
│   │
│   ├── Models/
│   │   ├── Company.php
│   │   ├── Invitation.php
│   │   ├── Project.php
│   │   ├── BoardColumn.php
│   │   └── User.php
│   │
│   ├── Policies/
│   │   └── CompanyPolicy.php
│   │
│   └── Traits/
│       └── CompanyScoped.php
│
├── database/
│   ├── factories/
│   │   ├── CompanyFactory.php
│   │   ├── ProjectFactory.php
│   │   ├── BoardColumnFactory.php
│   │   └── UserFactory.php
│   │
│   └── migrations/
│       ├── create_companies_table.php
│       ├── add_company_id_to_users_table.php
│       ├── add_role_to_users_table.php
│       ├── create_projects_table.php
│       ├── create_board_columns_table.php
│       └── create_invitations_table.php
│
├── resources/
│   └── views/
│       ├── dashboard.blade.php
│       ├── switch-company.blade.php
│       │
│       ├── invitations/
│       │   └── create.blade.php
│       │
│       ├── projects/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       │
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
│       ├── Authorization/
│       │   ├── RoleAuthorizationTest.php
│       │   ├── TenantIsolationTest.php
│       │   └── InvitationTest.php
│       │
│       └── Projects/
│           ├── ProjectTest.php
│           └── BoardColumnTest.php
│
└── README.md
```

---

# 🔄 Branching Strategy

The project follows a feature-branch workflow:

* `main` — Production-ready code.
* `feature/*` — Feature branches for individual tasks.

Each feature branch is implemented independently, tested, and merged into `main` after the task acceptance criteria are satisfied.

For example:

```text
main
 │
 ├── feature/task1
 │
 ├── feature/task2
 │
 └── feature/task3
```

Before merging a feature branch, the complete test suite should be run:

```bash
php artisan test
```

---

# 📄 License

This project is open-sourced software licensed under the MIT License.

---

# 👤 Author

**Jana Hassan**

GitHub: @jjanahassan

Email: janahassan210@yahoo.com
