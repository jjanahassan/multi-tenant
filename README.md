# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, tenant data isolation, project management, and customizable Kanban boards with task management.

## 🚀 Features

* **Multi-Tenant Architecture**: Each user belongs to a company with isolated data.
* **Authentication**: Registration, Login, Logout with Laravel Fortify.
* **Company Creation**: New users automatically create their own company.
* **Company Owner**: The first registered user becomes the company owner.
* **Role-Based Authorization**: Supports `owner`, `admin`, and `member` roles.
* **Tenant Data Isolation**: Users can only access data belonging to their own company.
* **Policy-Based Authorization**: Company and project actions are protected using Laravel Policies.
* **Invite Teammate**: Owners and admins can invite teammates with a specified role.
* **Queued Invitation Job**: Invitations dispatch a placeholder queued job for future email delivery.
* **Projects**: Companies can create, view, edit, and delete their own projects.
* **Customizable Kanban Boards**: Each project can have configurable board columns.
* **Board Column Management**: Board columns can be added, renamed, deleted, and reordered.
* **Default Board Columns**: New projects automatically receive `To Do`, `In Progress`, and `Done` columns.
* **Tasks**: Users can create, update, and delete tasks within project board columns.
* **Task Assignment**: Tasks can be assigned to users belonging to the same company.
* **Task Due Dates**: Tasks can optionally have a due date.
* **Task Positioning**: Tasks maintain a numeric position within their board column.
* **Drag & Drop**: Tasks can be visually dragged between Kanban columns.
* **AJAX Task Movement**: Moving a task between columns is persisted through an asynchronous endpoint.
* **Task Reordering**: Task positions are persisted when tasks are moved between columns.
* **Assignee Filtering**: Tasks can be filtered by their assigned user.
* **Due Date Filtering**: Tasks can be filtered by due date.
* **Due Date Sorting**: Tasks can be sorted by due date in ascending or descending order.
* **Request Validation**: Form Requests validate project, board column, and task operations.
* **Tenant-Scoped Projects**: Projects are automatically restricted to the authenticated user's company.
* **Tenant-Scoped Tasks**: Tasks are restricted to projects and users belonging to the authenticated user's company.
* **Switch Company**: Scaffold for switching between companies.
* **Comprehensive Testing**: The complete test suite verifies authentication, authorization, tenant isolation, projects, board columns, and task management.

---

## 📋 Requirements

* PHP >= 8.2
* Composer
* Node.js & NPM
* SQLite (default) or MySQL

---

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

### 3. Install Frontend Dependencies

```bash
npm install
```

### 4. Create the Environment File

```bash
cp .env.example .env
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
```

### 5. Generate the Application Key

```bash
php artisan key:generate
```

### 6. Configure the Database

The application uses SQLite by default.

Create the SQLite database:

```bash
touch database/database.sqlite
```

On Windows, create an empty file named:

```text
database/database.sqlite
```

Then make sure the `.env` file contains the appropriate database configuration.

For SQLite:

```env
DB_CONNECTION=sqlite
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Build Frontend Assets

```bash
npm run build
```

### 9. Start the Development Server

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

For frontend development with Vite, use:

```bash
npm run dev
```

---

## 🗄️ Database Structure

The application uses a company-based multi-tenant database structure.

### Companies

The `companies` table represents each tenant in the application.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Company name |
| `owner_id` | bigint | User who owns the company |
| `is_active` | boolean | Whether the company is active |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Users

The `users` table stores authenticated users and associates each user with a company.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | User name |
| `email` | string | Unique email address |
| `password` | string | Hashed password |
| `company_id` | bigint | User's company |
| `role` | string | `owner`, `admin`, or `member` |
| `email_verified_at` | timestamp | Email verification timestamp |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Projects

Projects belong to a company and are automatically tenant-scoped.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `company_id` | bigint | Owning company |
| `name` | string | Project name |
| `description` | text | Optional project description |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Board Columns

Each project contains customizable Kanban board columns.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `project_id` | bigint | Project the column belongs to |
| `name` | string | Column name |
| `position` | unsigned integer | Column order |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

New projects automatically receive:

1. `To Do`
2. `In Progress`
3. `Done`

### Tasks

Tasks belong to a project and a board column.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `project_id` | bigint | Project the task belongs to |
| `board_column_id` | bigint | Current board column |
| `assignee_id` | bigint | Assigned company user, nullable |
| `title` | string | Task title |
| `description` | text | Optional task description |
| `due_date` | date | Optional due date |
| `position` | unsigned integer | Position within the board column |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Invitations

Invitations are associated with companies and users and are used to invite teammates with a selected role.

---

## 🔗 Database Relationships

```text
Company
│
├── hasMany Users
├── hasMany Projects
└── hasMany Invitations
     │
     └── belongsTo User
     
User
│
├── belongsTo Company
└── hasMany Assigned Tasks

Project
│
├── belongsTo Company
├── hasMany Board Columns
└── hasMany Tasks

BoardColumn
│
├── belongsTo Project
└── hasMany Tasks

Task
│
├── belongsTo Project
├── belongsTo BoardColumn
└── belongsTo User (assignee)
```

---

## 🏢 Multi-Tenancy

The application uses company-based tenant isolation.

Each authenticated user belongs to a company through:

```text
users.company_id
```

Projects belong to companies through:

```text
projects.company_id
```

Tasks belong to projects, while board columns belong to projects.

Tenant isolation is enforced using the `CompanyScoped` trait and additional validation and authorization checks.

Users cannot access projects, tasks, board columns, or other company-specific data belonging to another company.

---

## 👥 Roles & Authorization

The application supports three roles:

### Owner

The company owner has the highest level of access.

Owners can:

* Manage company settings
* Invite teammates
* Remove users
* Manage projects
* Manage board columns
* Manage tasks
* Delete the company

### Admin

Admins can perform project and board management operations.

Admins can:

* Create projects
* Update projects
* Delete projects
* Add board columns
* Rename board columns
* Delete board columns
* Reorder board columns
* Create tasks
* Update tasks
* Delete tasks
* Invite teammates

### Member

Members have more limited permissions.

Members can access permitted company/project data but cannot perform owner/admin-only management actions such as inviting or removing teammates.

Authorization is implemented using Laravel Policies and Gates where appropriate.

---

## ✉️ Invitations

Owners and admins can invite teammates to their company.

An invitation contains:

* Email address
* Requested role
* Company
* User who created the invitation

The invitation system prevents invalid role assignments such as assigning the `owner` role through the invitation flow.

Invitation creation also dispatches a queued placeholder job that can later be connected to an email delivery system.

---

## 📁 Projects

Projects provide the main organizational level for company work.

Users with sufficient permissions can:

* Create projects
* View projects
* Edit projects
* Delete projects

Every project belongs to a company.

Projects are automatically scoped to the authenticated user's company to prevent cross-tenant access.

---

## 📋 Kanban Boards

Each project contains a customizable Kanban board.

### Default Columns

When a project is created, the following columns are automatically generated:

```text
To Do → In Progress → Done
```

### Column Management

Authorized users can:

* Add columns
* Rename columns
* Delete columns
* Move columns left
* Move columns right

Each column stores a numeric `position` value to maintain its order.

---

## ✅ Tasks

Tasks belong to both a project and a board column.

A task supports:

* Title
* Description
* Assignee
* Due date
* Board column
* Position

Tasks can be created directly from the Kanban board.

### Task Creation

When a task is created, its position is automatically calculated based on the highest existing position within its selected board column.

This allows tasks to maintain an ordered list inside each column.

---

## 👤 Task Assignment

Tasks can be assigned to users belonging to the same company.

The application validates the assignee using the project's company:

```text
users.company_id = projects.company_id
```

This prevents a task from being assigned to a user belonging to another company.

---

## 📅 Task Due Dates

Tasks may optionally have a due date.

Due dates are validated as dates and displayed directly on task cards.

Users can also filter and sort tasks based on their due dates.

---

## 🖱️ Drag & Drop

Tasks can be moved between Kanban columns using drag and drop.

Each task card is draggable:

```html
draggable="true"
```

The JavaScript implementation detects:

* The dragged task
* The source column
* The destination column
* The task's position within the destination

When a task is dropped into another column, the UI updates immediately and the move is persisted through the backend.

Empty columns also dynamically display:

```text
No tasks yet.
```

when their last task is moved away.

---

## 🔄 AJAX Task Movement

Task movement is handled asynchronously rather than requiring a full page reload.

The frontend sends the task movement information to the task move endpoint.

The endpoint validates:

* The project
* The destination board column
* The task
* The requested position
* Company ownership

A successful request returns a JSON response:

```json
{
    "success": true,
    "message": "Task moved successfully."
}
```

---

## 📍 Task Positioning

Each task contains a numeric `position` field.

Positions are maintained separately within each board column.

For example:

```text
To Do

Task A → position 0
Task B → position 1
Task C → position 2
```

If a task is moved to another column, its position is recalculated based on its new location.

This allows the Kanban board to preserve task ordering after drag-and-drop operations.

---

## 🔎 Task Filtering

The project board supports filtering tasks by assignee.

The user can select:

```text
All
```

or a specific company user.

The filtering is performed against the task's `assignee_id`.

Only users belonging to the current company are available as valid assignees.

---

## 📆 Due Date Filtering

Tasks can also be filtered based on their due date.

The UI supports selecting a due date and filtering the visible tasks accordingly.

This allows users to quickly identify tasks due on a particular date.

---

## ↕️ Due Date Sorting

Tasks can be sorted by due date.

Supported sorting directions include:

* Ascending
* Descending

Tasks without a due date are handled separately from tasks with assigned due dates so that sorting remains predictable.

---

## 🛡️ Validation

The application uses Laravel Form Requests for validation.

Examples include:

```text
StoreProjectRequest
UpdateProjectRequest
StoreBoardColumnRequest
UpdateBoardColumnRequest
StoreTaskRequest
UpdateTaskRequest
MoveTaskRequest
```

Validation includes checks such as:

* Required task title
* Valid board column
* Board column belongs to the current project
* Assignee belongs to the current company
* Valid due date
* Valid task position
* Valid project ownership

---

## 🔐 Tenant Isolation

Tenant isolation is enforced at multiple levels.

### Project Isolation

Projects use the `CompanyScoped` trait.

```text
Authenticated User
        ↓
User.company_id
        ↓
Project.company_id
```

Only projects belonging to the user's company are accessible.

### Board Column Isolation

Board columns are restricted through their project relationship.

```text
BoardColumn
    ↓
Project
    ↓
Company
```

### Task Isolation

Tasks are associated with a project and board column, and their operations verify that they belong to the correct project.

### Assignee Isolation

Task assignees must belong to the same company as the project.

---

## 🧪 Testing

The application includes feature tests covering authentication, authorization, tenant isolation, projects, board columns, tasks, task movement, filtering, and due-date functionality.

Run the complete test suite with:

```bash
php artisan test
```

Run tests for a specific feature using:

```bash
php artisan test --filter="TaskTest"
```

For task movement:

```bash
php artisan test --filter="TaskMoveTest"
```

For due-date functionality:

```bash
php artisan test --filter="due date"
```

The test suite verifies functionality including:

* User registration
* Authentication
* Company creation
* Role-based authorization
* Tenant isolation
* Invitations
* Project creation
* Project updates
* Project deletion
* Project validation
* Board column creation
* Board column renaming
* Board column deletion
* Board column reordering
* Board column tenant isolation
* Task creation
* Task updates
* Task deletion
* Task validation
* Task assignment
* Task movement
* Task position persistence
* Cross-project task protection
* Cross-company task protection
* Assignee filtering
* Due-date filtering
* Due-date sorting

Current test suite status:

```text
86 passed
193 assertions
1 skipped
1 risky
```

The skipped and risky tests are reported by PHPUnit/Pest but the implemented feature tests are passing.

---

## 🧰 Technologies Used

* **PHP**
* **Laravel**
* **Laravel Fortify**
* **Blade**
* **Tailwind CSS**
* **JavaScript**
* **SQLite**
* **Eloquent ORM**
* **Pest / PHPUnit**
* **Vite**
* **Git / GitHub**

---

## 📂 Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── BoardColumnController.php
│   │   ├── CompanyController.php
│   │   ├── InvitationController.php
│   │   ├── ProjectController.php
│   │   └── TaskController.php
│   │
│   └── Requests/
│       ├── StoreBoardColumnRequest.php
│       ├── UpdateBoardColumnRequest.php
│       ├── StoreTaskRequest.php
│       ├── UpdateTaskRequest.php
│       └── MoveTaskRequest.php
│
├── Models/
│   ├── BoardColumn.php
│   ├── Company.php
│   ├── Project.php
│   ├── Task.php
│   └── User.php
│
├── Policies/
│   ├── CompanyPolicy.php
│   └── ProjectPolicy.php
│
├── Jobs/
│   └── ...
│
└── Traits/
    └── CompanyScoped.php

database/
├── factories/
│   ├── BoardColumnFactory.php
│   ├── CompanyFactory.php
│   ├── ProjectFactory.php
│   └── TaskFactory.php
│
├── migrations/
│   ├── ..._create_companies_table.php
│   ├── ..._create_projects_table.php
│   ├── ..._create_board_columns_table.php
│   └── ..._create_tasks_table.php
│
└── database.sqlite

resources/
└── views/
    ├── projects/
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── index.blade.php
    │   └── show.blade.php
    │
    └── ...

routes/
└── web.php

tests/
└── Feature/
    ├── AuthenticationTest.php
    ├── BoardColumnTest.php
    ├── ProjectTest.php
    ├── TaskTest.php
    ├── TaskMoveTest.php
    └── ...
```

---

## 🛣️ Main Routes

### Projects

```text
GET     /projects
GET     /projects/create
POST    /projects
GET     /projects/{project}
GET     /projects/{project}/edit
PUT     /projects/{project}
DELETE  /projects/{project}
```

### Board Columns

```text
POST    /projects/{project}/columns
PUT     /projects/{project}/columns/{boardColumn}
DELETE  /projects/{project}/columns/{boardColumn}
PATCH   /projects/{project}/columns/{boardColumn}/reorder/{direction}
```

### Tasks

```text
POST    /projects/{project}/tasks
PUT     /projects/{project}/tasks/{task}
DELETE  /projects/{project}/tasks/{task}
```

The task movement endpoint is used asynchronously by the Kanban drag-and-drop functionality.

---

## ⚙️ Artisan Commands

Useful commands during development:

```bash
php artisan serve
```

```bash
php artisan migrate
```

```bash
php artisan migrate:fresh
```

```bash
php artisan migrate:fresh --seed
```

```bash
php artisan route:list
```

```bash
php artisan test
```

```bash
php artisan optimize:clear
```

---

## 🧹 Clearing Laravel Caches

If changes to routes, views, configuration, or application code are not appearing correctly, run:

```bash
php artisan optimize:clear
```

Then restart the development server if necessary.

---

## 🌱 Database Reset

During development, the database can be completely reset using:

```bash
php artisan migrate:fresh
```

This removes all existing tables and recreates the database schema.

Be careful when using this command because it deletes all existing database data.

---

## 🔀 Git Workflow

The project is developed using separate feature branches for each task/stage.

Example:

```bash
git checkout -b feature/task4
```

After completing a task:

```bash
git add .
git commit -m "Implement task management and Kanban functionality"
```

Push the branch:

```bash
git push -u origin feature/task4
```

Then create a Pull Request on GitHub targeting `main`.

---

## 📌 Development Tasks

### Task 1 — Project Skeleton, Multi-Tenancy Data Model & Authentication

Implemented:

* Company model and migration
* User-company relationship
* Company creation during registration
* Company ownership
* Authentication
* Login/logout
* Initial company switching scaffold

---

### Task 2 — Roles, Permissions & Tenant Isolation

Implemented:

* `owner`, `admin`, and `member` roles
* Role-based authorization
* Laravel Policies
* Company-scoped data
* Tenant isolation
* Invitation functionality
* Invitation authorization
* Queued invitation placeholder
* Company user management

---

### Task 3 — Projects & Boards CRUD

Implemented:

* Project model
* Project migration
* Project factory
* Project CRUD
* Project validation
* Company-scoped projects
* Board column model
* Board column migration
* Board column factory
* Default board columns
* Board column CRUD
* Column renaming
* Column deletion
* Column reordering
* Board column validation
* Board column tenant isolation

---

### Task 4 — Task Management & Kanban Functionality

Implemented:

* Task model
* Task migration
* Task factory
* Task CRUD
* Task validation
* Task assignment
* Company-safe task assignment
* Task due dates
* Task positioning
* Drag-and-drop task movement
* AJAX task movement
* Persisted task movement
* Persisted task positions
* Assignee filtering
* Due-date filtering
* Due-date sorting
* Task movement validation
* Cross-project task protection
* Cross-company task protection
* Automated task movement tests

---

## 🧩 Current Kanban Workflow

The complete workflow is:

```text
Create Company
      ↓
Register / Login User
      ↓
Create Project
      ↓
Default Columns Created
      ↓
To Do | In Progress | Done
      ↓
Create Tasks
      ↓
Assign Users / Add Due Dates
      ↓
Drag & Drop Tasks
      ↓
AJAX Move Request
      ↓
Validate Project + Column + Task
      ↓
Update Column + Position
      ↓
Persist Changes
      ↓
Filter / Sort Tasks
```

---

## 🔒 Security Considerations

The application uses several layers of protection:

* Authentication middleware
* Form Request authorization
* Laravel Policies
* Company-scoped Eloquent queries
* Project ownership validation
* Board column ownership validation
* Task-project relationship validation
* Same-company assignee validation
* Role-based authorization
* Route model binding with scoped bindings

Sensitive configuration values should never be committed to Git.

The `.env` file should remain ignored by Git.

---

## 📄 Environment Variables

The project uses Laravel's standard `.env` configuration.

Important variables include:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=sqlite
```

Additional Laravel mail, queue, cache, and frontend configuration can be added as needed.

---

## 🚫 Files That Should Not Be Committed

The following should remain excluded through `.gitignore`:

```text
.env
/node_modules
/vendor
/public/build
/storage/*.key
```

---

## 📖 Development Notes

The application is designed so that tenant isolation is not dependent solely on UI restrictions.

Even if a user attempts to manually access another company's resource through a URL or request, backend authorization and scoping prevent unauthorized access.

For example:

```text
Company A
    └── Project A
         └── Task A

Company B
    └── Project B
         └── Task B
```

A user belonging to Company A must never be able to access or modify Project B or Task B.

---

## 🎯 Future Improvements

Potential future improvements include:

* Full company switching between multiple memberships
* Real invitation email delivery
* Invitation acceptance flow
* Task editing directly from the Kanban card
* Task deletion directly from the Kanban card
* More advanced drag-and-drop positioning
* Real-time board updates
* Pagination for large projects
* Search functionality
* Notifications
* Activity logs
* More granular permissions
* Automated browser/UI tests
* Production deployment configuration

---


# 📄 License

This project is open-sourced software licensed under the MIT License.

---

# 👤 Author

**Jana Hassan**

GitHub: @jjanahassan

Email: janahassan210@yahoo.com
