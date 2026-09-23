# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, tenant data isolation, project management, customizable Kanban boards, tasks, comments, activity tracking, database notifications, background jobs, due-date reminders, a versioned public JSON API using Laravel Sanctum, automated testing, static analysis, and continuous integration.

## 🚀 Features

- **Multi-Tenant Architecture**: Each user belongs to a company and tenant data is isolated by company.

- **Authentication**: Registration, Login, Logout with Laravel Fortify.

- **Company Creation**: New users automatically create their own company.

- **Company Owner**: The first registered user becomes the company owner.

- **Role-Based Authorization**: Supports `owner`, `admin`, and `member` roles.

- **Tenant Isolation**: Users can only access data belonging to their own company.

- **Policies & Gates**: Backend authorization is enforced using Laravel Policies and Gates.

- **Company Switching**: Users can switch between their available companies.

- **Team Invitations**: Owners and admins can invite teammates with a selected role.

- **Queued Invitations**: Invitations dispatch a queued job for future email delivery.

- **Projects**: Company-scoped project CRUD.

- **Kanban Boards**: Each project has a customizable Kanban board.

- **Board Columns**: Columns can be added, renamed, deleted, and reordered.

- **Default Columns**: New projects automatically receive `To Do`, `In Progress`, and `Done`.

- **Tasks**: Create, update, delete, assign, move, and reorder tasks.

- **Task Assignment**: Tasks can only be assigned to users from the same company.

- **Due Dates**: Tasks support optional due dates.

- **Drag & Drop**: Tasks can be moved between Kanban columns.

- **AJAX Movement**: Task movement is persisted asynchronously without a full page reload.

- **Task Search**: Search tasks by title and description.

- **Task Filtering**: Filter by assignee, due-date range, and board column.

- **Due-Date Sorting**: Sort tasks by due date ascending or descending.

- **Combined Filters**: Search and filtering conditions can be combined.

- **Comments**: Users can add comments to tasks.

- **Activity Tracking**: Task creation, movement, assignment, and comments are recorded.

- **Database Notifications**: Users receive notifications for task assignments, comments, and due-date reminders.

- **Notification Read State**: Notifications can be marked as read using persistent `read_at` values.

- **Background Jobs**: Time-consuming operations are processed asynchronously.

- **Database Queue**: Laravel's database queue is used for background jobs.

- **Due-Date Reminders**: Tasks approaching their due date generate reminder notifications.

- **Scheduled Commands**: Laravel Scheduler automatically checks for due-soon tasks.

- **Retry & Backoff**: Reminder jobs retry automatically when processing fails.

- **Failed Job Handling**: Permanently failed jobs are stored in `failed_jobs`.

- **Idempotent Reminders**: Reminder processing prevents duplicate notifications.

- **Public JSON API**: Versioned API available under `/api/v1`.

- **Sanctum Authentication**: API access uses Laravel Sanctum bearer tokens.

- **Company-Bound API Tokens**: API access remains restricted to the user's company.

- **API Rate Limiting**: Authenticated API requests are limited to 60 requests per minute.

- **API Resources**: Dedicated Laravel API Resources provide consistent JSON responses.

- **Large Dataset Seeder**: Generates 25,000 tasks across multiple companies and projects.

- **N+1 Optimization**: Task assignees are eager-loaded to prevent repeated queries.

- **Database Indexing**: Indexes were added based on actual filtering and sorting requirements.

- **Performance Testing**: Board query count and load time are measured with large task datasets.

- **Feature Testing**: Authentication, authorization, tenant isolation, projects, tasks, comments, notifications, queues, API functionality, and performance are covered by tests.

- **Unit Testing**: Reusable application logic is covered with focused unit tests.

- **Static Analysis**: Larastan/PHPStan is used to detect type and code-quality issues.

- **Automated CI**: GitHub Actions runs formatting checks, static analysis, and the full test suite.

## 🛠️ Tech Stack

- **Framework**: Laravel 13

- **Language**: PHP

- **Authentication**: Laravel Fortify

- **API Authentication**: Laravel Sanctum

- **Frontend**: Blade

- **Database**: SQLite

- **Queue**: Laravel Database Queue

- **Testing**: Pest

- **Static Analysis**: Larastan / PHPStan

- **Build Tool**: Vite

- **Package Manager**: Composer / NPM

- **CI**: GitHub Actions

## 📋 Requirements

- PHP >= 8.5

- Composer

- Node.js & NPM

- SQLite or MySQL

## 🛠️ Installation

### 1. Clone the Repository

    git clone https://github.com/jjanahassan/multi-tenant.git

    cd multi-tenant

### 2. Install PHP Dependencies

    composer install

### 3. Install Frontend Dependencies

    npm install

### 4. Create the Environment File

    cp .env.example .env

For Windows PowerShell:

    Copy-Item .env.example .env

### 5. Generate the Application Key

    php artisan key:generate

### 6. Configure the Database

The application uses SQLite by default for local development. Production is deployed with PostgreSQL.

Create:

    database/database.sqlite

Then configure `.env`:

    DB_CONNECTION=sqlite

### 7. Configure the Queue

The application uses Laravel's database queue:

    QUEUE_CONNECTION=database

### 8. Run Migrations

    php artisan migrate

### 9. Build Frontend Assets

    npm run build

### 10. Start the Application

    php artisan serve

The application will be available at:

    http://127.0.0.1:8000

For frontend development:

    npm run dev

### 11. Start the Queue Worker

Background jobs require a running queue worker:

    php artisan queue:work

## 🗄️ Database Structure

### Companies

The `companies` table represents each tenant.

| Column | Type | Description |

| --- | --- | --- |

| `id` | bigint | Primary key |

| `name` | string | Company name |

| `owner_id` | bigint | Company owner |

| `is_active` | boolean | Whether the company is active |

| `created_at` | timestamp | Creation timestamp |

| `updated_at` | timestamp | Update timestamp |

### Users

Users belong to a company through `company_id`.

| Column | Type | Description |

| --- | --- | --- |

| `id` | bigint | Primary key |

| `name` | string | User name |

| `email` | string | Unique email |

| `password` | string | Hashed password |

| `company_id` | bigint | User's company |

| `role` | string | `owner`, `admin`, or `member` |

| `created_at` | timestamp | Creation timestamp |

| `updated_at` | timestamp | Update timestamp |

### Projects

Projects belong to a company.

| Column | Type | Description |

| --- | --- | --- |

| `id` | bigint | Primary key |

| `company_id` | bigint | Owning company |

| `name` | string | Project name |

| `description` | text | Optional description |

| `created_at` | timestamp | Creation timestamp |

| `updated_at` | timestamp | Update timestamp |

### Board Columns

Board columns belong to a project.

| Column | Type | Description |

| --- | --- | --- |

| `id` | bigint | Primary key |

| `project_id` | bigint | Parent project |

| `name` | string | Column name |

| `position` | unsigned integer | Column order |

| `created_at` | timestamp | Creation timestamp |

| `updated_at` | timestamp | Update timestamp |

New projects automatically receive:

    To Do → In Progress → Done

### Tasks

Tasks belong to projects and board columns.

| Column | Type | Description |

| --- | --- | --- |

| `id` | bigint | Primary key |

| `project_id` | bigint | Parent project |

| `board_column_id` | bigint | Current board column |

| `assignee_id` | bigint | Assigned user, nullable |

| `title` | string | Task title |

| `description` | text | Optional description |

| `due_date` | date | Optional due date |

| `position` | unsigned integer | Position within column |

| `created_at` | timestamp | Creation timestamp |

| `updated_at` | timestamp | Update timestamp |

### Comments

Comments use a polymorphic relationship and are currently associated with tasks.

### Activities

Activities record important task events such as:

    TaskCreated

    TaskMoved

    TaskAssigned

    CommentAdded

### Notifications

Laravel's database notification system is used for:

    task_assigned

    task_commented

    task_due_soon

Notifications contain a persistent `read_at` value.

### Due-Date Reminders

The `due_date_reminders` table provides persistent tracking for reminder processing.

The reminder record uses:

    task_id + assignee_id + due_date

as a unique combination to prevent duplicate reminders.

### Queue Tables

Laravel's database queue uses:

    jobs

    failed_jobs

to manage pending and failed background jobs.

## 🔗 Database Relationships

    Company

    │

    ├── hasMany Users

    ├── hasMany Projects

    └── hasMany Invitations

    User

    │

    ├── belongsTo Company

    ├── hasMany Assigned Tasks

    └── hasMany Comments

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

    ├── belongsTo User (assignee)

    ├── morphMany Comments

    ├── hasMany Activities

    └── hasMany Due Date Reminders

    Comment

    │

    ├── belongsTo User

    └── morphTo Commentable

    DueDateReminder

    │

    ├── belongsTo Task

    └── belongsTo User

## 🏢 Multi-Tenancy

The application uses company-based multi-tenancy.

Every authenticated user belongs to a company:

    users.company_id

Projects belong to companies:

    projects.company_id

Tasks belong to projects, and board columns belong to projects.

Tenant isolation is enforced through:

- `CompanyScoped`

- Laravel Policies

- Form Request authorization

- Company ownership checks

- Project relationships

- Same-company assignee validation

- API authorization

A user from Company A cannot access or modify Company B's projects or tasks.

## 👥 Roles & Authorization

The application supports three roles.

### Owner

Owners can:

- Manage company settings

- Invite teammates

- Remove users

- Manage projects

- Manage board columns

- Manage tasks

- Delete the company

### Admin

Admins can:

- Create projects

- Update projects

- Delete projects

- Manage board columns

- Create tasks

- Update tasks

- Delete tasks

- Invite teammates

### Member

Members can access permitted company and project data but cannot perform owner/admin-only management operations.

Authorization is enforced on the backend using Laravel Policies and Gates rather than relying only on UI visibility.

## ✉️ Invitations

Owners and admins can invite teammates.

An invitation contains:

- Email

- Company

- Role

- Inviting user

The invitation flow validates role assignments and prevents invalid owner-role invitations.

Invitation creation also dispatches a queued placeholder job for future email delivery.

## 📁 Projects

Projects are company-scoped.

Authorized users can:

- Create projects

- View projects

- Edit projects

- Delete projects

Every project belongs to one company.

## 📋 Kanban Boards

Each project automatically receives three default columns:

    To Do

    In Progress

    Done

Authorized users can:

- Add columns

- Rename columns

- Delete columns

- Reorder columns

Columns use a numeric `position` value to maintain their order.

## ✅ Tasks

Tasks support:

- Title

- Description

- Assignee

- Due date

- Board column

- Position

Task creation calculates an appropriate position within the selected board column.

Tasks can be moved between columns using drag-and-drop.

## 👤 Task Assignment

Tasks can only be assigned to users belonging to the same company as the project.

When a task is assigned:

    TaskAssigned Event

            ↓

    Activity Listener

            ↓

    Activity Record

    TaskAssigned Event

            ↓

    Notification Listener

            ↓

    Database Notification

The user performing the assignment does not receive a notification for their own action.

## 🔎 Task Search & Filtering

The project board supports:

- Search by title

- Search by description

- Assignee filtering

- Due-date range filtering

- Board-column filtering

- Due-date sorting

- Combined filters

### Search

The search query checks:

    title

    description

using the current contains-search implementation.

### Assignee Filtering

Tasks can be filtered using:

    assignee_id

The selected assignee is validated against the current company's users.

### Due-Date Filtering

Tasks can be filtered using:

    due_from

    due_to

The application validates that `due_to` is not earlier than `due_from`.

### Column Filtering

Tasks can be filtered by:

    column_id

The selected column is validated against the current project.

### Due-Date Sorting

Tasks can be sorted by:

    sort_due_date=asc

    sort_due_date=desc

### Combined Filtering

Filters can be combined in a single request.

For example:

    Search: report

    Assignee: User A

    Due From: 2026-09-01

    Due To: 2026-09-30

    Column: In Progress

All applicable filters are applied together at the database query level.

## 💬 Comments

Users can add comments to tasks.

Comments contain:

- User

- Task

- Body

- Timestamps

When a comment is added:

    CommentAdded Event

            ↓

    Activity Record

            ↓

    Comment Notification

The user who added the comment does not receive a notification for their own comment.

## 📜 Activity Log

Activity tracking uses Laravel Events and Listeners.

Tracked actions include:

    TaskCreated

    TaskMoved

    TaskAssigned

    CommentAdded

The event-driven structure keeps activity tracking separate from the core controller operations.

## 🔔 Notifications

The application uses Laravel database notifications.

### Task Assignment

The task assignee receives a notification when assigned to a task.

### Task Comments

The task assignee receives a notification when another user comments on their task.

### Due-Date Reminders

The task assignee receives a reminder when the task approaches its due date.

### Read State

Unread notifications have:

    read_at = null

When marked as read:

    read_at = current timestamp

The read state is persisted in the database.

## 🧵 Background Jobs & Queues

The application uses Laravel's database-backed queue system.

Configure:

    QUEUE_CONNECTION=database

Start the worker:

    php artisan queue:work

### Due-Date Reminder Workflow

    Laravel Scheduler

          ↓

    tasks:send-due-date-reminders

          ↓

    Find due-soon tasks

          ↓

    Dispatch SendDueDateReminder Job

          ↓

    Database Queue

          ↓

    Queue Worker

          ↓

    SendDueDateReminder

          ↓

    Database Notification

### Reminder Command

Run manually:

    php artisan tasks:send-due-date-reminders

The command identifies eligible due-soon tasks and dispatches reminder jobs.

### Scheduled Execution

The command is registered with Laravel Scheduler and runs daily.

Check configured schedules:

    php artisan schedule:list

### Retry Configuration

Due-date reminder jobs use multiple attempts with progressive backoff.

The retry configuration is designed to handle temporary failures without immediately marking the job as permanently failed.

### Failed Jobs

Failed jobs are stored in:

    failed_jobs

View failed jobs:

    php artisan queue:failed

Retry a failed job:

    php artisan queue:retry <id>

Forget a failed job:

    php artisan queue:forget <id>

### Idempotency

Reminder processing uses persistent reminder records and deterministic notification identifiers.

The unique reminder combination:

    task_id + assignee_id + due_date

prevents duplicate reminder records for the same task and user.

## 🖱️ Drag & Drop

Tasks can be dragged between board columns.

The frontend tracks:

- Dragged task

- Source column

- Destination column

- Destination position

The movement is sent to the backend using an asynchronous request.

The backend validates the task, project, board column, and tenant relationship before persisting the change.

## 🔄 Task Movement

Task movement updates:

- `board_column_id`

- `position`

A successful movement dispatches the `TaskMoved` event.

The event is then used to create the corresponding activity record.

## 🛡️ Validation

Laravel Form Requests are used to validate application operations.

Validation covers:

- Projects

- Board columns

- Tasks

- Task movement

- Comments

- Assignees

- Due dates

- Positions

- Tenant relationships

Examples include:

    StoreProjectRequest

    UpdateProjectRequest

    StoreBoardColumnRequest

    UpdateBoardColumnRequest

    StoreTaskRequest

    UpdateTaskRequest

    MoveTaskRequest

    StoreCommentRequest

    TaskFilterRequest

## 🌐 Public JSON API

The public API is versioned under:

    /api/v1

The API uses Laravel Sanctum for authentication.

### API Authentication

Create a token:

    POST /api/v1/auth/token

Example request:

    {

        "email": "user@example.com",

        "password": "password",

        "device_name": "Postman"

    }

Authenticated requests use:

    Authorization: Bearer <token>

    Accept: application/json

### Company

    GET /api/v1/company

Returns the authenticated user's company.

### Projects

    GET    /api/v1/projects

    POST   /api/v1/projects

    GET    /api/v1/projects/{project}

    PUT    /api/v1/projects/{project}

    DELETE /api/v1/projects/{project}

### Tasks

    GET    /api/v1/projects/{project}/tasks

    POST   /api/v1/projects/{project}/tasks

    GET    /api/v1/tasks/{task}

    PUT    /api/v1/tasks/{task}

    DELETE /api/v1/tasks/{task}

### API Resources

Dedicated Laravel API Resources provide consistent JSON responses for:

- Companies

- Projects

- Tasks

### API Security

API tokens are associated with users and their companies.

API authorization verifies that requested projects and tasks belong to the authenticated user's company.

Cross-company resources cannot be accessed through the API.

### API Rate Limiting

Authenticated API requests are limited to:

    60 requests per minute

Requests exceeding the limit receive:

    429 Too Many Requests

### API Workflow

The complete API workflow covers:

    Authenticate

        ↓

    Create Project

        ↓

    Create Task

        ↓

    Update Task

        ↓

    Move Task

        ↓

    Delete Task

A Postman collection is included for API testing.

## ⚡ Performance & Scalability

Task 8 focused on making the task board usable at larger data volumes.

The performance pass covered:

- Search

- Filtering

- Large dataset generation

- N+1 query detection

- Eager loading

- Database indexing

- Query-count measurement

- Board load-time measurement

## 🌱 Large Dataset Seeder

`LargeDatasetSeeder` generates:

    5 companies

    50 users

    25 projects

    75 board columns

    25,000 tasks

Each company contains:

    10 users

    5 projects

Each project contains:

    3 board columns

    1,000 tasks

Tasks are generated using factories and inserted in batches.

### Run the Seeder

    php artisan db:seed --class=LargeDatasetSeeder

Expected totals:

    Companies: 5

    Users: 50

    Projects: 25

    Tasks: 25,000

## 🔍 N+1 Query Detection & Fix

The board was measured using Laravel's database query log before and after eager-loading optimization.

### Before Optimization

A board containing 20 tasks produced:

    28 total database queries

The query log showed:

    select * from "users" where "users"."id" = ? limit 1

This query was executed once for each of the 20 tasks.

The result was:

    1 task query

    20 individual assignee queries

This was an N+1 query problem caused by lazy-loading each task's assignee relationship.

### Fix

The task assignee relationship was eager-loaded in `ProjectController::show()`.

The task query now uses:

    ->with([

        'assignee',

        'comments.user',

    ]);

Laravel therefore loads the required assignees in a single query using a `WHERE IN` condition instead of performing one query for every task.

### After Optimization

The same board containing 20 tasks produced:

    9 total database queries

The 20 individual assignee queries were replaced with one eager-loading query:

    select * from "users" where "users"."id" in (...)

The measured result was:

    Before: 28 queries

    After: 9 queries

    Before: 20 individual assignee queries

    After: 1 eager-loading query

This reduced the total query count by 19 queries, approximately 68%.

A regression test was added to the existing:

    tests/Feature/TaskTest.php

to ensure the board does not return to the N+1 query pattern.

## 🗂️ Database Indexes

Indexes were added based on the actual filtering and sorting requirements instead of indexing every database column.

### Existing Task Indexes

The `tasks` table already contains:

    $table->index(['project_id', 'board_column_id']);

    $table->index(['assignee_id']);

The composite index:

    project_id + board_column_id

supports task queries involving projects and their board columns.

The `assignee_id` index supports filtering tasks by assigned user.

These indexes were retained and duplicate indexes were not added.

### Due-Date Index

Task 8 introduced due-date filtering and sorting, so an index was added to:

    tasks.due_date

The migration adds:

    $table->index('due_date');

This supports queries involving:

    due_from

    due_to

    due_date sorting

The index can be removed during rollback using:

    $table->dropIndex(['due_date']);

### Why Other Indexes Were Not Added

A separate `project_id` index was not added because `project_id` is already the leftmost column of:

    (project_id, board_column_id)

A duplicate `assignee_id` index was not added because one already exists.

A standard B-tree index was not added to `title` or `description` because the current search implementation uses:

    LIKE '%search%'

A leading wildcard generally prevents a normal B-tree index from being useful for this type of contains-search.

If search requirements grow significantly in the future, a dedicated full-text search solution could be considered.

## 📊 Performance Validation

A dedicated performance test was added to the existing:

    tests/Feature/TaskTest.php

The test measured the task board with a large number of tasks.

The measured result was:

    Board load time: 251.41 ms

    Board query count: 9

The performance test passed with:

    2 assertions

The board therefore completed the measured performance test with:

    9 database queries

    251.41 ms measured load time

The large dataset seeder independently provides a realistic dataset containing 25,000 tasks for large-scale testing.

## 🧪 Testing

The application contains feature tests covering:

- Authentication

- Registration

- Login

- Logout

- Authorization

- Roles

- Tenant isolation

- Invitations

- Projects

- Board columns

- Tasks

- Task assignment

- Task movement

- Task positioning

- Search

- Assignee filtering

- Due-date filtering

- Column filtering

- Combined filtering

- Comments

- Activity tracking

- Notifications

- Background jobs

- Queue processing

- Due-date reminders

- API authentication

- API resources

- API tenant isolation

- API project CRUD

- API task operations

- Large dataset seeding

- N+1 query prevention

- Database indexes

- Board performance

Task 8 task-related tests were added to the existing:

    tests/Feature/TaskTest.php

### Run the Complete Test Suite

    php artisan test

### Run Task Tests

    php artisan test tests/Feature/TaskTest.php

### Run the Performance Test

    php artisan test tests/Feature/TaskTest.php --filter="task board remains efficient with a large number of tasks"

Expected performance output:

    Board load time: 251.41 ms

    Board query count: 9

### Run the Large Dataset Seeder Test

    php artisan test tests/Feature/LargeDatasetSeederTest.php

### Run Due-Date Reminder Tests

    php artisan test tests/Feature/SendDueDateReminderJobTest.php

    php artisan test tests/Feature/SendDueDateRemindersCommandTest.php

### Run Comment Tests

    php artisan test --filter="CommentTest"

### Run Notification Tests

    php artisan test --filter="NotificationTest"

## 🧪 Automated Quality Checks

Task 9 introduced automated quality checks for the application.

### Pest Test Suite

The complete Laravel test suite can be run using:

    php artisan test

The test suite covers the major application workflows including:

- Tenant isolation

- Authorization

- Kanban task movement

- Activity logging

- Notifications

- API authentication

- API workflows

- Search and filtering

- Background jobs

- Performance-related behavior

### Unit Tests

Reusable application logic is covered by focused unit tests.

The `PositionCalculator` service handles position calculations used by tasks and board columns.

Unit coverage includes:

    No existing positions → position 0

    Maximum position 0 → position 1

    Maximum position 4 → position 5

    Maximum position 10 → position 11

Run the unit tests:

    php artisan test tests/Unit

### Static Analysis

Larastan/PHPStan is configured at level 7.

The project uses:

    larastan/larastan

    phpstan/phpstan

Static analysis covers:

    app/

    bootstrap/app.php

    config/

    database/

    routes/

Run PHPStan with:

    vendor/bin/phpstan analyse --memory-limit=512M

The Composer quality workflow also runs PHPStan through:

    composer test

The PHPStan memory limit is configured in the Composer `types:check` script to support analysis of the full Laravel application.

### Code Formatting

Laravel Pint is included in the automated quality workflow.

Check formatting with:

    vendor/bin/pint --test

### Complete Quality Workflow

Run the complete local quality workflow with:

    composer test

The workflow performs:

    Laravel configuration cache clearing

            ↓

    Pint formatting check

            ↓

    PHPStan/Larastan static analysis

            ↓

    Complete Pest/Laravel test suite

## 🔄 Continuous Integration

Task 9 introduced GitHub Actions CI.

The workflow is located at:

    .github/workflows/ci.yml

The workflow runs automatically on:

- Pushes to `main`

- Pushes to feature branches

- Pull requests targeting `main`

### CI Workflow

    GitHub Push / Pull Request

            ↓

    Checkout Repository

            ↓

    Setup PHP 8.5

            ↓

    Setup Node.js

            ↓

    Validate Composer

            ↓

    Install Composer Dependencies

            ↓

    Install NPM Dependencies

            ↓

    Build Frontend Assets

            ↓

    Prepare Laravel Environment

            ↓

    Run Composer Test Workflow

            ↓

    Pint

            ↓

    PHPStan / Larastan

            ↓

    Laravel Test Suite

### Frontend Build

CI installs the frontend dependencies and builds the production assets:

    npm ci

    npm run build

This ensures that the Laravel views relying on Vite assets can be tested in a clean CI environment.

### Laravel Environment

The CI environment prepares Laravel using:

    cp .env.example .env

    php artisan key:generate

    touch database/database.sqlite

This provides the application key and SQLite database required by the automated test suite.

### CI Configuration

The workflow uses:

    PHP 8.5

    Node.js 22

    SQLite

    Composer

    NPM

    Laravel Pint

    PHPStan / Larastan

    Pest

### Local Equivalent

The main local quality command is:

    composer test

The CI workflow is designed to reproduce the same formatting, static-analysis, and test checks in a clean GitHub Actions environment.

## 📂 Project Structure

    app/

    ├── Actions/

    │   └── Fortify/

    │       └── CreateNewUser.php

    │

    ├── Console/

    │   └── Commands/

    │       └── SendDueDateReminders.php

    │

    ├── Events/

    │   ├── CommentAdded.php

    │   ├── TaskAssigned.php

    │   ├── TaskCreated.php

    │   └── TaskMoved.php

    │

    ├── Http/

    │   ├── Controllers/

    │   │   ├── BoardColumnController.php

    │   │   ├── CommentController.php

    │   │   ├── CompanyController.php

    │   │   ├── InvitationController.php

    │   │   ├── NotificationController.php

    │   │   ├── ProjectController.php

    │   │   └── TaskController.php

    │   │

    │   └── Requests/

    │       ├── StoreBoardColumnRequest.php

    │       ├── UpdateBoardColumnRequest.php

    │       ├── StoreCommentRequest.php

    │       ├── StoreProjectRequest.php

    │       ├── UpdateProjectRequest.php

    │       ├── StoreTaskRequest.php

    │       ├── UpdateTaskRequest.php

    │       ├── MoveTaskRequest.php

    │       └── TaskFilterRequest.php

    │

    ├── Jobs/

    │   ├── SendDueDateReminder.php

    │   └── ...

    │

    ├── Listeners/

    │   ├── LogTaskActivity.php

    │   ├── SendTaskAssignedNotification.php

    │   └── SendTaskCommentedNotification.php

    │

    ├── Models/

    │   ├── Activity.php

    │   ├── BoardColumn.php

    │   ├── Comment.php

    │   ├── Company.php

    │   ├── DueDateReminder.php

    │   ├── Invitation.php

    │   ├── Project.php

    │   ├── Task.php

    │   └── User.php

    │

    ├── Notifications/

    │   ├── DueDateReminderNotification.php

    │   ├── TaskAssignedNotification.php

    │   └── TaskCommentedNotification.php

    │

    ├── Policies/

    │   ├── CompanyPolicy.php

    │   └── ProjectPolicy.php

    │

    ├── Services/

    │   └── PositionCalculator.php

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

    │   ├── ..._create_tasks_table.php

    │   ├── ..._create_comments_table.php

    │   ├── ..._create_activities_table.php

    │   ├── ..._create_due_date_reminders_table.php

    │   ├── ..._create_notifications_table.php

    │   ├── ..._create_jobs_table.php

    │   ├── ..._create_failed_jobs_table.php

    │   └── ..._add_due_date_index_to_tasks_table.php

    │

    └── seeders/

        ├── DatabaseSeeder.php

        └── LargeDatasetSeeder.php

    resources/

    └── views/

        ├── notifications/

        ├── projects/

        └── ...

    routes/

    ├── api.php

    ├── console.php

    └── web.php

    tests/

    ├── Feature/

    │   ├── AuthenticationTest.php

    │   ├── BoardColumnTest.php

    │   ├── CommentTest.php

    │   ├── NotificationTest.php

    │   ├── ProjectTest.php

    │   ├── TaskMoveTest.php

    │   ├── TaskTest.php

    │   ├── LargeDatasetSeederTest.php

    │   ├── SendDueDateReminderJobTest.php

    │   └── SendDueDateRemindersCommandTest.php

    │

    └── Unit/

        └── ExampleTest.php

    .github/

    └── workflows/

        └── ci.yml

## 🛣️ Main Web Routes

### Projects

    GET     /projects

    GET     /projects/create

    POST    /projects

    GET     /projects/{project}

    GET     /projects/{project}/edit

    PUT     /projects/{project}

    DELETE  /projects/{project}

### Board Columns

    POST    /projects/{project}/columns

    PUT     /projects/{project}/columns/{boardColumn}

    DELETE  /projects/{project}/columns/{boardColumn}

### Tasks

    POST    /projects/{project}/tasks

    PUT     /projects/{project}/tasks/{task}

    DELETE  /projects/{project}/tasks/{task}

    PATCH   /projects/{project}/tasks/{task}/move

### Comments

    POST    /tasks/{task}/comments

    DELETE  /comments/{comment}

### Notifications

    GET     /notifications

    POST    /notifications/{notification}/read

## ⚙️ Useful Artisan Commands

    php artisan serve

    php artisan migrate

    php artisan migrate:fresh

    php artisan migrate:fresh --seed

    php artisan db:seed --class=LargeDatasetSeeder

    php artisan route:list

    php artisan test

    php artisan optimize:clear

### Queue Commands

    php artisan queue:work

    php artisan queue:work --once

    php artisan queue:failed

    php artisan queue:retry <id>

    php artisan queue:forget <id>

### Reminder Commands

    php artisan tasks:send-due-date-reminders

    php artisan schedule:list

## 🧹 Clearing Laravel Caches

If changes to routes, configuration, views, or application code are not appearing correctly:

    php artisan optimize:clear

## 🗃️ Database Reset

Reset the database:

    php artisan migrate:fresh

Reset and seed:

    php artisan migrate:fresh --seed

Reset and generate the large performance dataset:

    php artisan migrate:fresh

    php artisan db:seed --class=LargeDatasetSeeder

## 🔀 Git Workflow

Development is organized into feature branches.

Create a feature branch:

    git checkout -b feature/task9

Commit changes:

    git add .

    git commit -m "your commit message"

Push the branch:

    git push -u origin feature/task9

Then create a Pull Request targeting `main`.

After the Pull Request is reviewed and merged:

    git checkout main

    git pull origin main

    git branch -d feature/task9

## 📌 Development Tasks

### Task 1 — Project Skeleton, Multi-Tenancy & Authentication

Implemented:

- Company model and migration

- User-company relationship

- Company creation during registration

- Company ownership

- Fortify authentication

- Login/logout

- Company switching scaffold

### Task 2 — Roles, Permissions & Tenant Isolation

Implemented:

- Owner/admin/member roles

- Role-based authorization

- Laravel Policies

- Tenant isolation

- Company-scoped data

- Team invitations

- Invitation authorization

- Queued invitation job

- Company user management

### Task 3 — Projects & Boards CRUD

Implemented:

- Project CRUD

- Project validation

- Company-scoped projects

- Board column CRUD

- Default board columns

- Column renaming

- Column deletion

- Column reordering

- Board column validation

- Tenant isolation

### Task 4 — Task Management & Kanban Functionality

Implemented:

- Task CRUD

- Task validation

- Task assignment

- Same-company assignee validation

- Task due dates

- Task positioning

- Drag-and-drop movement

- AJAX task movement

- Persisted task positions

- Task filters

- Task movement authorization

- Cross-company protection

### Task 5 — Comments, Activity Log & Notifications

Implemented:

- Task comments

- Comment validation

- Comment ownership

- Activity tracking

- Task events

- Activity listeners

- Database notifications

- Assignment notifications

- Comment notifications

- Notification read state

- Notification UI

### Task 6 — Background Jobs & Due-Date Reminders

Implemented:

- Database queue

- Failed jobs

- Due-date reminder job

- Due-date reminder notification

- Reminder command

- Daily scheduler

- Retry configuration

- Backoff configuration

- Failed job handling

- Idempotent reminder processing

- Reminder tracking

- Queue tests

### Task 7 — Public JSON API & Token Authentication

Implemented:

- Laravel Sanctum

- API token authentication

- Company-bound API tokens

- API rate limiting

- Versioned `/api/v1` routes

- Company endpoint

- Project CRUD API

- Task API

- API Resources

- JSON responses

- API validation

- API tenant isolation

- API workflow tests

- Postman collection

- API documentation

### Task 8 — Search, Filtering & Performance Pass

Implemented:

- Task search

- Assignee filtering

- Due-date range filtering

- Board-column filtering

- Due-date sorting

- Combined filters

- Large dataset seeding

- 25,000-task dataset

- N+1 query detection

- Eager loading

- N+1 regression testing

- Due-date database indexing

- Query-count validation

- Board performance testing

- Performance documentation

### Task 9 — Automated Test Suite & CI

Implemented:

- Feature test coverage audit across Tasks 1–8

- Missing task movement activity coverage

- Tenant isolation test coverage

- Authorization test coverage

- Kanban movement test coverage

- Notification test coverage

- API workflow test coverage

- Unit tests for reusable position logic

- `PositionCalculator` service

- Shared position calculation for tasks and board columns

- Larastan/PHPStan static analysis

- PHPStan level 7 configuration

- Static-analysis fixes across application code

- Model PHPDoc/type improvements

- Explicit type narrowing for polymorphic relationships

- PHPStan memory configuration

- GitHub Actions CI workflow

- PHP 8.5 CI environment

- Node.js frontend build in CI

- Composer validation

- NPM dependency installation

- Vite production build

- Laravel environment preparation in CI

- Automated Pint checks

- Automated PHPStan/Larastan checks

- Automated Laravel/Pest test suite

## 📈 Task 8 Results

### Search & Filtering

The board supports combined:

    Search

    +

    Assignee

    +

    Due Date Range

    +

    Board Column

    +

    Due Date Sorting

### Large Dataset

    5 companies

    50 users

    25 projects

    75 board columns

    25,000 tasks

### N+1 Optimization

    Before: 28 queries

    After: 9 queries

    Before: 20 individual assignee queries

    After: 1 eager-loading query

Query reduction:

    19 fewer queries

    ≈68% reduction

### Board Performance

    1,000 tasks

    9 database queries

    251.41 ms measured board load time

### Task 10 — Deployment & Handover

Implemented:

Real production deployment using Railway
Production PostgreSQL database
Production environment configuration
APP_ENV=production
APP_DEBUG=false
Production APP_KEY
Production database connection using Railway PostgreSQL
Production frontend asset build using Vite
Production Laravel configuration, event, route, and view caching
Dedicated production queue worker service
Background worker running php artisan queue:work
Verified queued task-assignment notifications in production
Verified queued task-comment notifications in production
Dedicated production scheduler service
Railway cron schedule invoking php artisan schedule:run
Five-minute scheduler invocation
Daily due-date reminder schedule at 09:00
Production failed-job inspection and retry commands
Production deployment and handover documentation
Known limitations and future improvement documentation

## 📈 Task 9 Results

### Test Coverage

Task 9 audited the existing feature-test suite and added missing coverage for important application behavior.

The suite covers:

    Tenant Isolation

    Authorization

    Kanban Movement

    Activity Logging

    Notifications

    API Authentication

    API Workflows

    Search & Filtering

    Background Jobs

    Performance

### Unit Test Coverage

Reusable position logic was extracted into:

    app/Services/PositionCalculator.php

The service is covered by unit tests for:

    Initial position calculation

    Incrementing positions

    Position calculation after an existing maximum

### Static Analysis

PHPStan/Larastan was configured at:

    Level 7

The analysis covers:

    app/

    bootstrap/app.php

    config/

    database/

    routes/

Static-analysis issues identified during Task 9 were resolved across controllers, requests, resources, models, jobs, listeners, notifications, factories, routes, traits, and configuration.

PHPStan is executed with:

    vendor/bin/phpstan analyse --memory-limit=512M

### Automated CI

GitHub Actions was added at:

    .github/workflows/ci.yml

The CI pipeline performs:

    Composer validation

    Composer dependency installation

    NPM dependency installation

    Vite production build

    Laravel environment setup

    Pint formatting check

    PHPStan/Larastan static analysis

    Full Laravel/Pest test suite

This provides an automated quality gate for pull requests and pushes.

## 🔒 Security Considerations

The application uses multiple layers of authorization and tenant protection:

- Authentication middleware

- Form Request authorization

- Laravel Policies

- Company-scoped queries

- Project ownership validation

- Board column ownership validation

- Task-project validation

- Same-company assignee validation

- Role-based authorization

- Sanctum bearer tokens

- Company-bound API access

- API rate limiting

Sensitive configuration values should never be committed to Git.

The `.env` file remains excluded through `.gitignore`.

## 🚫 Files Not Committed to Git

The following files/directories should remain ignored:

    .env

    /node_modules

    /vendor

    /public/build

    /storage/*.key

## 🔄 Application Workflow

    Create Company

          ↓

    Register / Login

          ↓

    Assign Role

          ↓

    Invite Teammates

          ↓

    Create Project

          ↓

    Create Default Board Columns

          ↓

    Create Tasks

          ↓

    Search / Filter / Assign / Add Due Dates

          ↓

    Drag & Drop Tasks

          ↓

    Persist Movement

          ↓

    Log Activity

          ↓

    Add Comments

          ↓

    Send Notifications

          ↓

    Mark Notifications as Read

          ↓

    Daily Reminder Scheduler

          ↓

    Dispatch Reminder Jobs

          ↓

    Queue Worker

          ↓

    Due-Date Reminder Notification

## 🔄 Event-Driven Architecture

    TaskCreated

    TaskMoved

    TaskAssigned

    CommentAdded

          ↓

    Event Listeners

          ↓

    Activities / Notifications

This keeps secondary behavior such as activity logging and notifications separate from the main task operations.

## 🧵 Background Processing Architecture

    Laravel Scheduler

          ↓

    Artisan Command

          ↓

    Queue Dispatch

          ↓

    Database Queue

          ↓

    Queue Worker

          ↓

    Background Job

          ↓

    Notification

## 🤖 CI Quality Gate

The project now uses automated checks to validate changes before they are merged.

    Developer Push / Pull Request

              ↓

          GitHub Actions

              ↓

        Composer Validation

              ↓

          Pint Check

              ↓

     PHPStan / Larastan

              ↓

         Laravel Tests

              ↓

           CI Result

This reduces the risk of merging changes that introduce formatting issues, static-analysis errors, or failing automated tests.

## 🚀 Production Deployment & Handover

Task 10 deploys the application to a real production environment using Railway.

Production Architecture

The production deployment is separated into dedicated services:

                         Railway
                            │
             ┌──────────────┼──────────────┐
             │              │              │
          Web Service   Queue Worker   Scheduler
             │              │              │
        Laravel app    queue:work     schedule:run
             │              │              │
             └──────────────┼──────────────┘
                            │
                       PostgreSQL
Production Services

Web Service

Runs the Laravel application and serves the production website.

The application is started using Railway's assigned port:

php artisan serve --host=0.0.0.0 --port=$PORT

Queue Worker

A separate Railway service runs:

php artisan queue:work

This keeps queued work outside the web request lifecycle.

Queued notifications were verified in production, including:

TaskAssignedNotification
TaskCommentedNotification

The worker successfully processed these jobs and the receiving member received the notifications.

Scheduler

A separate Railway service runs:

php artisan schedule:run

Railway invokes the scheduler every five minutes:

*/5 * * * *

Laravel then determines whether an application schedule is due.

The current application schedule is:

0 9 * * * php artisan tasks:send-due-date-reminders

The due-date reminder command therefore runs daily at 09:00 according to the application's configured schedule.

Production Environment

Production uses:

APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=pgsql
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database
MAIL_MAILER=log
BROADCAST_CONNECTION=log

The production database is PostgreSQL.

Sensitive values such as APP_KEY and database credentials are configured through Railway environment variables and are not committed to Git.

All production services use the same production APP_KEY and database configuration.

Production Build

The Railway build performs:

composer install --optimize-autoloader --no-interaction
npm run build
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

The frontend production assets are generated through Vite.

The deployment uses PHP 8.5 and installs the PostgreSQL PDO extension (pdo_pgsql).

Database Deployment

Production migrations are run using Laravel's production-safe migration command:

php artisan migrate --force

The production database is PostgreSQL rather than the SQLite database used for local development.

Queue Operations

Inspect failed jobs:

php artisan queue:failed

Retry a failed job:

php artisan queue:retry <id>

Forget a failed job:

php artisan queue:forget <id>

The database-backed queue allows queued notifications and reminder jobs to be processed independently from web requests.

Scheduler Operations

Inspect the configured schedules:

php artisan schedule:list

Manually trigger the scheduler:

php artisan schedule:run

Run the reminder command directly when testing:

php artisan tasks:send-due-date-reminders

The Railway scheduler repeatedly invokes schedule:run, while Laravel remains responsible for determining which scheduled commands are due.

Production Verification

The production deployment was verified for:

Application startup
Production PostgreSQL connectivity
Authentication
Projects
Tasks
Task assignment
Task comments
Database notifications
Queued notifications
Queue worker processing
Production frontend assets
Laravel scheduler execution
Failed-job inspection commands

The production queue worker was specifically verified by observing successful execution of:

TaskAssignedNotification ... DONE
TaskCommentedNotification ... DONE

The production scheduler was verified by repeated successful executions of:

php artisan schedule:run

When no scheduled command is due, Laravel reports:

No scheduled commands are ready to run.

This is expected behavior between scheduled execution times.

Production URL

Production application:

https://multi-tenant-production-e7c4.up.railway.app

Short Live Walkthrough

The recommended live demonstration flow is:

Open the production application.
Log in as a company owner/admin.
Open a project.
Create or edit a task.
Assign the task to a company member.
Show the member receiving the assignment notification.
Add a comment to the task.
Show the corresponding comment notification.
Move the task between Kanban columns.
Show the activity record.
Open the notification dropdown and mark a notification as read.
Demonstrate the /api/v1 API using a Sanctum bearer token.
Show the Railway queue worker processing queued notifications.
Show the Railway scheduler executing php artisan schedule:run.
Show php artisan queue:failed for failed-job inspection.
Handover Checklist

A new engineer taking over the project should have:

Repository access
Railway project/service access
Production environment variable access
Production APP_KEY
PostgreSQL access
Web service configuration
Queue worker configuration
Scheduler configuration
GitHub Actions access
Knowledge of the local setup instructions
Knowledge of the test and static-analysis commands
Knowledge of the queue and scheduler commands

Sensitive production credentials should be transferred through a secure secret-management process rather than committed to the repository.

Architecture Decisions Across Tasks 1–10

Task 1 — Multi-tenancy and authentication

Company-based tenancy was introduced at the data model and authentication level. Registration creates the user's company and establishes the initial owner.

Task 2 — Authorization and tenant isolation

Roles, Policies, Gates, company relationships, and invitation authorization were added so tenant boundaries are enforced in backend application logic.

Task 3 — Projects and boards

Projects became company-scoped resources, while board columns belong to projects. Default columns are created automatically.

Task 4 — Tasks and Kanban

Tasks belong to projects and columns rather than directly storing a company ID. Task access therefore follows the project's tenant boundary. Position values support Kanban ordering and drag-and-drop movement.

Task 5 — Events, activities, and notifications

Events and listeners separate secondary behavior such as activity logging and notifications from the core controller operations.

Task 6 — Background processing

Laravel's database queue was selected for asynchronous work. Due-date reminders use a scheduled command, queued jobs, retries, backoff, failed-job tracking, and idempotent reminder records.

Task 7 — Public API

Laravel Sanctum provides bearer-token authentication. API resources provide consistent JSON responses, while company-bound authorization preserves tenant boundaries.

Task 8 — Search and performance

Filtering and sorting were implemented at the database-query level. Eager loading, targeted indexes, large-dataset seeding, and query-count testing address larger task-board workloads.

Task 9 — Quality and CI

Pest tests, Laravel Pint, Larastan/PHPStan, and GitHub Actions provide automated quality checks before changes are merged.

Task 10 — Production deployment

The application was deployed to Railway with PostgreSQL, a dedicated queue worker, and a dedicated scheduler. Production builds generate frontend assets and cache Laravel configuration, routes, events, and views.

Known Limitations

The current implementation has several known limitations:

Invitation email delivery is still represented by a queued placeholder job rather than a fully integrated email provider.
Invitation acceptance is not implemented as a complete user-facing flow.
Task search currently uses contains matching (LIKE '%search%') rather than a dedicated full-text search solution.
Production queues use the database driver rather than Redis.
Production caching uses the database cache rather than Redis.
Mail delivery is configured with the log mail driver unless a production mail provider is configured.
The current Railway deployment uses a simple application server setup rather than a more advanced managed Laravel platform configuration.
Board rendering and task interaction could be improved further for very large datasets.
Monitoring, centralized logging, and alerting are not yet implemented.
API token abilities/scopes could be expanded.
The production deployment requires Railway project/service access for operational changes.
What I Would Do With More Time

Potential next improvements include:

Integrate a real transactional email provider.
Complete the invitation acceptance workflow.
Introduce Redis for queues and cache.
Add production monitoring, error tracking, and alerting.
Add full-text search for large task datasets.
Add pagination or lazy loading for very large boards.
Improve Kanban positioning for complex drag-and-drop scenarios.
Expand API resources and token abilities.
Add more detailed operational dashboards.
Add automated production smoke tests.
Improve deployment automation and zero-downtime deployment practices.
Add more extensive tenant-isolation regression tests for every resource.

## 🎯 Future Improvements

Potential future improvements include:

- Real invitation email delivery

- Invitation acceptance flow

- More advanced company switching

- Task editing directly from Kanban cards

- Task deletion directly from Kanban cards

- More advanced drag-and-drop positioning

- Pagination or lazy loading for extremely large boards

- Full-text task search for larger datasets

- Additional API resources and endpoints

- API token abilities/scopes

- Production caching

- Redis queues for production

- Additional performance monitoring

- Production database optimization

---

# 📄 License

This project is open-sourced software licensed under the MIT License.

---

## 👤 Author

**Jana Hassan**

GitHub: [@jjanahassan](https://github.com/jjanahassan)

Email: [janahassan210@yahoo.com](mailto:janahassan210@yahoo.com)