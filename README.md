# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, tenant data isolation, project management, customizable Kanban boards, tasks, comments, activity tracking, notifications, background jobs, and a versioned public JSON API using Laravel Sanctum.

## 🚀 Features

- **Multi-Tenant Architecture**: Each user belongs to a company and tenant data is isolated by company.
- **Authentication**: Registration, Login, Logout using Laravel Fortify.
- **Company Creation**: New users automatically create their own company.
- **Company Owner**: The first registered user becomes the company owner.
- **Role-Based Authorization**: Supports owner, admin, and member roles.
- **Tenant Isolation**: Users can only access data belonging to their own company.
- **Project Management**: Create, view, update, and delete projects.
- **Customizable Kanban Boards**: Each project has configurable board columns.
- **Task Management**: Create, update, move, assign, filter, and delete tasks.
- **Task Comments**: Users can comment on tasks.
- **Activity Tracking**: Important task actions are recorded in an activity log.
- **Database Notifications**: Users receive notifications for relevant task activity.
- **Background Jobs**: Due-date reminder notifications are processed asynchronously using Laravel queues.
- **Scheduled Commands**: Due-date reminder jobs are dispatched through Laravel's scheduler.
- **Public JSON API**: Versioned `/api/v1` API for mobile and third-party integrations.
- **API Token Authentication**: Laravel Sanctum bearer tokens.
- **Company-Bound API Tokens**: Each API token is restricted to the user's company.
- **API Resources**: Consistent JSON resource structures for companies, projects, and tasks.
- **API Rate Limiting**: Authenticated API requests are limited to 60 requests per minute.
- **Postman Collection**: API requests are included for manual API testing.

---

## 🛠️ Tech Stack

- **Laravel 13**
- **PHP 8.5**
- **SQLite**
- **Laravel Fortify**
- **Laravel Sanctum**
- **Blade**
- **Livewire / Flux**
- **Pest**
- **Laravel Queues**
- **Laravel Scheduler**
- **RESTful JSON API**

---

# 🏢 Multi-Tenant Architecture

The application uses a company-based multi-tenant architecture.

Each user belongs to a company through:

    users.company_id

Company-owned records are associated with their company.

Tenant isolation is enforced through:

- Company-scoped models where appropriate.
- Laravel Policies.
- Authorization checks.
- Relationship-based tenant checks.
- API token company binding.

Users cannot access another company's projects or related data.

---

# 🔐 Authentication

Authentication is implemented using Laravel Fortify.

Supported authentication functionality includes:

- User registration.
- User login.
- User logout.
- Company creation during registration.
- Automatic assignment of the registering user as company owner.

When a user registers:

1. A company is created.
2. The user is assigned to the company.
3. The user receives the `owner` role.
4. The company's `owner_id` is assigned to the user.

---

# 👥 Roles and Permissions

The application supports three roles:

    owner
    admin
    member

### Owner

The owner has full company-level permissions, including:

- Managing company members.
- Inviting users.
- Removing users.
- Creating projects.
- Updating projects.
- Deleting projects.
- Managing company resources.

### Admin

Admins can manage operational company resources such as:

- Projects.
- Tasks.
- Invitations.

### Member

Members have restricted access based on the relevant policy.

Authorization is enforced server-side using Laravel Policies and Gates rather than relying only on UI restrictions.

---

# 📁 Project Management

Projects belong to a company.

A project contains:

- Name.
- Description.
- Company.
- Board columns.
- Tasks.

Project CRUD functionality includes:

    GET    /projects
    GET    /projects/create
    POST   /projects
    GET    /projects/{project}
    GET    /projects/{project}/edit
    PUT    /projects/{project}
    DELETE /projects/{project}

Projects are restricted to the authenticated user's company.

---

# 📋 Kanban Boards

Each project automatically receives default board columns when it is created:

    To Do
    In Progress
    Done

Board columns support:

- Creation.
- Renaming.
- Deletion.
- Reordering.

Each board column belongs to a project.

Tasks can be moved between columns by updating their `board_column_id`.

---

# ✅ Task Management

Tasks belong to projects.

Task fields include:

- Title.
- Description.
- Project.
- Board column.
- Assignee.
- Due date.
- Position.

Tasks support:

- Creation.
- Viewing.
- Updating.
- Assignment.
- Due dates.
- Reordering.
- Moving between board columns.
- Deletion.

Task access is authorized through the task's project and company relationship.

---

# 💬 Comments

Users can add comments to tasks.

Comments are associated with their commentable resource and include the user who created the comment.

Comment functionality supports:

- Creating comments.
- Displaying comments.
- Associating comments with tasks.
- Recording comment activity.

---

# 📊 Activity Tracking

The application records important task activity.

Examples include:

- Task created.
- Task assigned.
- Task moved.
- Comment added.

Activities are associated with the relevant task and provide a record of changes made within the application.

---

# 🔔 Notifications

The application uses Laravel database notifications.

Supported notification types include:

    task_assigned
    task_commented

Users can:

- View notifications.
- See unread notifications.
- Mark notifications as read.

Task assignment and commenting notifications are implemented as queued notifications.

---

# ⚙️ Background Jobs and Queues

The application uses Laravel's database queue for asynchronous background processing.

Due-date reminder functionality includes:

- A reminder job.
- A scheduled Artisan command.
- Database-backed queue processing.
- Idempotency protection.
- Retry handling.
- Failure handling.

The scheduled command identifies tasks requiring reminders and dispatches the corresponding jobs.

Jobs are processed outside the HTTP request lifecycle.

---

# 🕐 Task 6 — Due-Date Reminders

Task 6 implemented the background-job workflow for task due-date reminders.

The workflow is:

    Scheduler
        ↓
    Reminder Command
        ↓
    Reminder Job
        ↓
    Notification

The implementation uses Laravel's database queue and scheduler to process reminders asynchronously.

The workflow includes protection against duplicate reminders and supports retries and failed jobs.

---

# 🌐 Task 7 — Public JSON API + Token Authentication

Task 7 adds a versioned public JSON API for mobile applications and third-party integrations using Laravel Sanctum.

All API endpoints are versioned under:

    /api/v1

The API supports:

- Sanctum token authentication.
- Company-bound API tokens.
- Company information.
- Project CRUD.
- Task CRUD.
- Task movement between board columns.
- API Resources.
- JSON validation responses.
- Authentication error responses.
- Tenant isolation.
- API rate limiting.
- Postman testing.

---

# 🔑 API Authentication

API authentication uses Laravel Sanctum bearer tokens.

A token can be requested using:

    POST /api/v1/auth/token

Request body:

    {
        "email": "user@example.com",
        "password": "password",
        "device_name": "Postman"
    }

A successful authentication response is:

    {
        "token": "...",
        "token_type": "Bearer",
        "company_id": 1
    }

The returned token is then used for authenticated API requests:

    Authorization: Bearer <token>

The API token is bound to the authenticated user's company.

A user must belong to a company in order to create an API token.

---

# 🏢 Company API

The authenticated user's company can be retrieved using:

    GET /api/v1/company

This endpoint requires a valid Sanctum bearer token.

The response uses the `CompanyResource`.

---

# 📁 Project API

The API provides full CRUD functionality for projects.

### List Projects

    GET /api/v1/projects

### Create Project

    POST /api/v1/projects

Example request:

    {
        "name": "Example Project",
        "description": "Example project description."
    }

### Get Project

    GET /api/v1/projects/{project}

### Update Project

    PUT /api/v1/projects/{project}

Example request:

    {
        "name": "Updated Project",
        "description": "Updated description."
    }

### Delete Project

    DELETE /api/v1/projects/{project}

All project operations are restricted to the authenticated user's company.

---

# ✅ Task API

Tasks are accessed through their project for listing and creation.

### List Project Tasks

    GET /api/v1/projects/{project}/tasks

### Create Task

    POST /api/v1/projects/{project}/tasks

Example request:

    {
        "title": "Example Task",
        "description": "Example task description.",
        "board_column_id": 1,
        "assignee_id": 1,
        "due_date": null
    }

Individual tasks can then be accessed directly.

### Get Task

    GET /api/v1/tasks/{task}

### Update / Move Task

    PUT /api/v1/tasks/{task}

Example request:

    {
        "title": "Example Task",
        "description": "Example task description.",
        "board_column_id": 3,
        "assignee_id": 1,
        "due_date": null
    }

Changing `board_column_id` moves the task between Kanban columns.

### Delete Task

    DELETE /api/v1/tasks/{task}

---

# 📦 API Resources

Laravel API Resources are used to provide structured JSON responses.

The following resources are implemented:

    CompanyResource
    ProjectResource
    TaskResource

### Company Resource

    {
        "data": {
            "id": 1,
            "name": "Example Company",
            "is_active": true
        }
    }

### Project Resource

    {
        "data": {
            "id": 1,
            "name": "Example Project",
            "description": "Example description.",
            "company_id": 1,
            "created_at": "...",
            "updated_at": "..."
        }
    }

### Task Resource

    {
        "data": {
            "id": 1,
            "title": "Example Task",
            "description": "Example description.",
            "project_id": 1,
            "board_column_id": 1,
            "assignee_id": 1,
            "due_date": null,
            "position": 0,
            "created_at": "...",
            "updated_at": "..."
        }
    }

---

# ❌ API Error Responses

API requests return JSON responses for authentication and validation failures.

Unauthenticated requests return:

    {
        "message": "Unauthenticated."
    }

with:

    401 Unauthorized

Unauthorized access returns a JSON error response with:

    403 Forbidden

Laravel validation errors use Laravel's standard JSON validation response format.

The API is configured to render JSON responses for requests under:

    /api/*

and requests that explicitly expect JSON.

---

# 🔒 API Tenant Isolation

API tenant isolation is enforced at multiple levels.

API tokens are bound to a single company.

For example:

    Company A
        ↓
    User A
        ↓
    Company A API Token

The Company A token cannot access Company B's projects or tasks.

Even if a user knows the ID of another company's resource, the API prevents access.

### Projects

Projects contain a direct:

    company_id

and are protected using company-scoped queries and project authorization policies.

### Tasks

Tasks belong to projects rather than directly storing a `company_id`.

Therefore, task authorization verifies the company through the task's project relationship.

This prevents a user from accessing a task belonging to another company's project.

---

# 🚦 API Rate Limiting

Authenticated API routes use the Laravel `api` rate limiter.

The current limit is:

    60 requests per minute

The rate limit is applied using:

    throttle:api

Requests exceeding the limit receive:

    429 Too Many Requests

The limiter identifies authenticated requests using the authenticated user's ID and falls back to the request IP address when no authenticated user is available.

---

# 🧪 Testing

The application uses Pest for automated testing.

Tests cover:

- Authentication.
- Registration.
- Company creation.
- Company ownership.
- Roles and permissions.
- Tenant isolation.
- Project CRUD.
- Board column functionality.
- Task CRUD.
- Task movement.
- Task assignment.
- Due dates.
- Comments.
- Activity logging.
- Notifications.
- Background jobs.
- Queue processing.
- API authentication.
- API token/company binding.
- API resources.
- API validation.
- API rate limiting.
- Project API CRUD.
- Task API CRUD.
- Cross-company API isolation.
- Complete API workflow.

---

# 🌐 Task 7 API Tests

Dedicated API tests are located under:

    tests/Feature/Api/

The API test suite includes coverage for:

- Sanctum authentication.
- Token authentication.
- Company-bound tokens.
- Company switching / token mismatch protection.
- Rate limiting.
- API Resources.
- Company API.
- Project API.
- Task API.
- Task movement.
- Tenant isolation.
- Complete API acceptance workflow.

The complete acceptance workflow verifies:

    Authenticate
        ↓
    Create Project
        ↓
    Automatic Board Columns
        ↓
    Create Task
        ↓
    Move Task
        ↓
    Delete Task

The tenant isolation workflow verifies that:

    Company A Token
           ↓
    Company B Project → Denied
    Company B Task    → Denied
    Company B Tasks   → Denied

---

# 📮 Postman API Collection

A Postman collection is included for manually testing the public API.

The collection is located under:

    postman/TeamBoard API v1/

It contains requests for:

    Authentication
    Company
    Projects
    Tasks

The intended API testing workflow is:

    Get API Token
          ↓
    Create Project
          ↓
    Get/List Project
          ↓
    Create Task
          ↓
    Get Task
          ↓
    Move Task
          ↓
    Delete Task

The Postman collection uses the local Laravel API:

    http://127.0.0.1:8000/api/v1

API requests require a valid Sanctum bearer token except for the token-generation endpoint.

---

# 🧰 Running the Application

Clone the repository:

    git clone <repository-url>
    cd multi-tenant-app

Install PHP dependencies:

    composer install

Install frontend dependencies:

    npm install

Create the environment file by copying:

    .env.example

to:

    .env

Generate the application key:

    php artisan key:generate

Configure the database in `.env`.

For SQLite, create:

    database/database.sqlite

Run migrations:

    php artisan migrate

Start the Laravel development server:

    php artisan serve

The application will normally be available at:

    http://127.0.0.1:8000

---

# 🧪 Running Tests

Run the complete test suite:

    php artisan test

Run only API tests:

    php artisan test tests/Feature/Api

Run the API acceptance workflow test:

    php artisan test tests/Feature/Api/ApiWorkflowTest.php

Clear Laravel caches when necessary:

    php artisan optimize:clear

---

# ⚙️ Queue Processing

For the database queue, run:

    php artisan queue:work

The scheduler can be run locally using:

    php artisan schedule:work

These processes allow queued jobs and scheduled tasks to run during development.

---

# 🔧 Useful Artisan Commands

Clear cached application data:

    php artisan optimize:clear

Run migrations:

    php artisan migrate

Open Laravel Tinker:

    php artisan tinker

Run tests:

    php artisan test

---

# 📌 Task Progress

## Task 1 — Project Skeleton, Tenancy Model & Authentication

Completed:

- Laravel project skeleton.
- Company model.
- Company ownership.
- User/company relationship.
- Registration flow.
- Authentication.
- Tenant-aware architecture.
- Initial feature tests.

---

## Task 2 — Roles, Permissions & Tenant Isolation

Completed:

- Owner/admin/member roles.
- Role-based authorization.
- Company policies.
- Tenant isolation.
- Invitations.
- Member management.
- Cross-company access tests.
- Authorization tests.

---

## Task 3 — Projects & Boards CRUD

Completed:

- Project CRUD.
- Project policies.
- Project validation.
- Project/company relationship.
- Automatic default board columns.
- Board column management.
- Board column ordering.
- Validation tests.

---

## Task 4 — Tasks & Kanban Workflow

Completed:

- Task model.
- Task CRUD.
- Task assignment.
- Due dates.
- Task positioning.
- Kanban task movement.
- Task filtering.
- Task sorting.
- Authorization.
- Tenant isolation.

---

## Task 5 — Comments, Activity & Notifications

Completed:

- Task comments.
- Activity logging.
- Task assignment notifications.
- Comment notifications.
- Database notifications.
- Notification dropdown.
- Mark-as-read functionality.
- Notification tests.

---

## Task 6 — Background Jobs & Queues

Completed:

- Database queue configuration.
- Due-date reminder jobs.
- Scheduled reminder command.
- Laravel scheduler integration.
- Idempotency protection.
- Retry handling.
- Failed job handling.
- Queue-related tests.

---

## Task 7 — Public JSON API + Token Auth

Completed:

- Laravel Sanctum installation.
- Sanctum bearer token authentication.
- Company-bound API tokens.
- API token company validation middleware.
- Versioned `/api/v1` routes.
- API Resources.
- JSON API response handling.
- API validation responses.
- Company API.
- Full Project CRUD API.
- Project task listing and creation.
- Task retrieval.
- Task update and movement.
- Task deletion.
- API tenant isolation.
- API rate limiting.
- API acceptance workflow tests.
- Cross-company security tests.
- Postman collection.
- API documentation.

---

# 📡 API Endpoint Summary

## Authentication

    POST /api/v1/auth/token

## Company

    GET /api/v1/company

## Projects

    GET    /api/v1/projects
    POST   /api/v1/projects
    GET    /api/v1/projects/{project}
    PUT    /api/v1/projects/{project}
    DELETE /api/v1/projects/{project}

## Tasks

    GET    /api/v1/projects/{project}/tasks
    POST   /api/v1/projects/{project}/tasks
    GET    /api/v1/tasks/{task}
    PUT    /api/v1/tasks/{task}
    DELETE /api/v1/tasks/{task}

---

# 🔐 Security Summary

The application applies authorization at the server level.

Security mechanisms include:

- Laravel authentication.
- Laravel Policies.
- Company-scoped data access.
- Sanctum bearer token authentication.
- Company-bound API tokens.
- Token/company validation middleware.
- Tenant-aware project access.
- Relationship-based task authorization.
- API rate limiting.
- JSON authentication errors.
- Validation through Form Requests.
- Protected API routes.

API requests cannot bypass tenant isolation simply by guessing another company's resource IDs.

---

# 📂 Project Structure

Important application directories include:

    app/
    ├── Http/
    │   ├── Controllers/
    │   │   └── Api/
    │   │       └── V1/
    │   ├── Requests/
    │   ├── Resources/
    │   └── Middleware/
    ├── Models/
    ├── Policies/
    └── Jobs/

    database/
    ├── factories/
    ├── migrations/
    └── seeders/

    resources/
    └── views/

    routes/
    ├── web.php
    └── api.php

    tests/
    └── Feature/
        └── Api/

    postman/
    └── TeamBoard API v1/

---

# 📜 Development Workflow

Feature development follows a branch-based workflow.

Each task is implemented on a dedicated feature branch.

Example:

    git checkout main
    git pull origin main
    git checkout -b feature/task7

Changes are committed using descriptive commit messages.

Example:

    git commit -m "feat: add sanctum api authentication"

After completing a task:

    git push -u origin feature/task7

A Pull Request is then created from:

    feature/task7

into:

    main

The branch is reviewed before being merged.

---

# 📋 Task 7 Commit Stages

Task 7 was organized into the following stages:

    feat: add sanctum api authentication

    feat: add versioned api resources and response format

    feat: add company and project api endpoints

    feat: add task api endpoints

    feat: harden api authentication and rate limiting

    test: cover complete api workflow and tenant isolation

---

# 🎯 API Acceptance Criteria

Task 7 is considered complete when the following workflow succeeds entirely through the API:

    1. Authenticate
    2. Create a project
    3. Create a task
    4. Move the task to another board column
    5. Delete the task

The API must also ensure:

    Company A token
            ↓
    Company A resources → Allowed

    Company A token
            ↓
    Company B resources → Denied

The API provides:

- Versioned endpoints.
- Sanctum authentication.
- Company-bound tokens.
- Tenant isolation.
- Consistent JSON responses.
- Validation errors.
- Rate limiting.
- Automated tests.
- Manual Postman testing support.

---

# 📄 License

This project is open-sourced software licensed under the MIT License.

---

## 👤 Author

**Jana Hassan**

GitHub: [@jjanahassan](https://github.com/jjanahassan)

Email: [janahassan210@yahoo.com](mailto:janahassan210@yahoo.com)