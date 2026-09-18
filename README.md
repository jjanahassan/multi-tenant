# Multi-Tenant Laravel Application

A multi-tenant Laravel application with company-based authentication, role-based authorization, tenant data isolation, project management, customizable Kanban boards, tasks, comments, activity tracking, database notifications, background jobs, due-date reminders, and a versioned public JSON API using Laravel Sanctum.

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

## 🛠️ Tech Stack

- **Framework**: Laravel 13
- **Language**: PHP
- **Authentication**: Laravel Fortify
- **API Authentication**: Laravel Sanctum
- **Frontend**: Blade
- **Database**: SQLite
- **Queue**: Laravel Database Queue
- **Testing**: Pest
- **Build Tool**: Vite
- **Package Manager**: Composer / NPM

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite or MySQL

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

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 5. Generate the Application Key

```bash
php artisan key:generate
```

### 6. Configure the Database

The application uses SQLite by default.

Create:

```text
database/database.sqlite
```

Then configure `.env`:

```env
DB_CONNECTION=sqlite
```

### 7. Configure the Queue

The application uses Laravel's database queue:

```env
QUEUE_CONNECTION=database
```

### 8. Run Migrations

```bash
php artisan migrate
```

### 9. Build Frontend Assets

```bash
npm run build
```

### 10. Start the Application

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

For frontend development:

```bash
npm run dev
```

### 11. Start the Queue Worker

Background jobs require a running queue worker:

```bash
php artisan queue:work
```

## 🗄️ Database Structure

### Companies

The `companies` table represents each tenant.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Company name |
| `owner_id` | bigint | Company owner |
| `is_active` | boolean | Whether the company is active |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Users

Users belong to a company through `company_id`.

| Column | Type | Description |
|---|---|---|
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
|---|---|---|
| `id` | bigint | Primary key |
| `company_id` | bigint | Owning company |
| `name` | string | Project name |
| `description` | text | Optional description |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

### Board Columns

Board columns belong to a project.

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Primary key |
| `project_id` | bigint | Parent project |
| `name` | string | Column name |
| `position` | unsigned integer | Column order |
| `created_at` | timestamp | Creation timestamp |
| `updated_at` | timestamp | Update timestamp |

New projects automatically receive:

```text
To Do → In Progress → Done
```

### Tasks

Tasks belong to projects and board columns.

| Column | Type | Description |
|---|---|---|
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

```text
TaskCreated
TaskMoved
TaskAssigned
CommentAdded
```

### Notifications

Laravel's database notification system is used for:

```text
task_assigned
task_commented
task_due_soon
```

Notifications contain a persistent `read_at` value.

### Due-Date Reminders

The `due_date_reminders` table provides persistent tracking for reminder processing.

The reminder record uses:

```text
task_id + assignee_id + due_date
```

as a unique combination to prevent duplicate reminders.

### Queue Tables

Laravel's database queue uses:

```text
jobs
failed_jobs
```

to manage pending and failed background jobs.

## 🔗 Database Relationships

```text
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
```

## 🏢 Multi-Tenancy

The application uses company-based multi-tenancy.

Every authenticated user belongs to a company:

```text
users.company_id
```

Projects belong to companies:

```text
projects.company_id
```

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

```text
To Do
In Progress
Done
```

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

```text
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
```

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

```text
title
description
```

using the current contains-search implementation.

### Assignee Filtering

Tasks can be filtered using:

```text
assignee_id
```

The selected assignee is validated against the current company's users.

### Due-Date Filtering

Tasks can be filtered using:

```text
due_from
due_to
```

The application validates that `due_to` is not earlier than `due_from`.

### Column Filtering

Tasks can be filtered by:

```text
column_id
```

The selected column is validated against the current project.

### Due-Date Sorting

Tasks can be sorted by:

```text
sort_due_date=asc
sort_due_date=desc
```

### Combined Filtering

Filters can be combined in a single request.

For example:

```text
Search: report
Assignee: User A
Due From: 2026-09-01
Due To: 2026-09-30
Column: In Progress
```

All applicable filters are applied together at the database query level.

## 💬 Comments

Users can add comments to tasks.

Comments contain:

- User
- Task
- Body
- Timestamps

When a comment is added:

```text
CommentAdded Event
        ↓
Activity Record
        ↓
Comment Notification
```

The user who added the comment does not receive a notification for their own comment.

## 📜 Activity Log

Activity tracking uses Laravel Events and Listeners.

Tracked actions include:

```text
TaskCreated
TaskMoved
TaskAssigned
CommentAdded
```

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

```text
read_at = null
```

When marked as read:

```text
read_at = current timestamp
```

The read state is persisted in the database.

## 🧵 Background Jobs & Queues

The application uses Laravel's database-backed queue system.

Configure:

```env
QUEUE_CONNECTION=database
```

Start the worker:

```bash
php artisan queue:work
```

### Due-Date Reminder Workflow

```text
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
```

### Reminder Command

Run manually:

```bash
php artisan tasks:send-due-date-reminders
```

The command identifies eligible due-soon tasks and dispatches reminder jobs.

### Scheduled Execution

The command is registered with Laravel Scheduler and runs daily.

Check configured schedules:

```bash
php artisan schedule:list
```

### Retry Configuration

Due-date reminder jobs use multiple attempts with progressive backoff.

The retry configuration is designed to handle temporary failures without immediately marking the job as permanently failed.

### Failed Jobs

Failed jobs are stored in:

```text
failed_jobs
```

View failed jobs:

```bash
php artisan queue:failed
```

Retry a failed job:

```bash
php artisan queue:retry <id>
```

Forget a failed job:

```bash
php artisan queue:forget <id>
```

### Idempotency

Reminder processing uses persistent reminder records and deterministic notification identifiers.

The unique reminder combination:

```text
task_id + assignee_id + due_date
```

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

```text
StoreProjectRequest
UpdateProjectRequest
StoreBoardColumnRequest
UpdateBoardColumnRequest
StoreTaskRequest
UpdateTaskRequest
MoveTaskRequest
StoreCommentRequest
TaskFilterRequest
```

## 🌐 Public JSON API

The public API is versioned under:

```text
/api/v1
```

The API uses Laravel Sanctum for authentication.

### API Authentication

Create a token:

```http
POST /api/v1/auth/token
```

Example request:

```json
{
    "email": "user@example.com",
    "password": "password",
    "device_name": "Postman"
}
```

Authenticated requests use:

```http
Authorization: Bearer <token>
Accept: application/json
```

### Company

```http
GET /api/v1/company
```

Returns the authenticated user's company.

### Projects

```http
GET    /api/v1/projects
POST   /api/v1/projects
GET    /api/v1/projects/{project}
PUT    /api/v1/projects/{project}
DELETE /api/v1/projects/{project}
```

### Tasks

```http
GET    /api/v1/projects/{project}/tasks
POST   /api/v1/projects/{project}/tasks
GET    /api/v1/tasks/{task}
PUT    /api/v1/tasks/{task}
DELETE /api/v1/tasks/{task}
```

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

```text
60 requests per minute
```

Requests exceeding the limit receive:

```text
429 Too Many Requests
```

### API Workflow

The complete API workflow covers:

```text
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
```

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

```text
5 companies
50 users
25 projects
75 board columns
25,000 tasks
```

Each company contains:

```text
10 users
5 projects
```

Each project contains:

```text
3 board columns
1,000 tasks
```

Tasks are generated using factories and inserted in batches.

### Run the Seeder

```bash
php artisan db:seed --class=LargeDatasetSeeder
```

Expected totals:

```text
Companies: 5
Users: 50
Projects: 25
Tasks: 25,000
```

## 🔍 N+1 Query Detection & Fix

The board was measured using Laravel's database query log before and after eager-loading optimization.

### Before Optimization

A board containing 20 tasks produced:

```text
28 total database queries
```

The query log showed:

```sql
select * from "users" where "users"."id" = ? limit 1
```

This query was executed once for each of the 20 tasks.

The result was:

```text
1 task query
20 individual assignee queries
```

This was an N+1 query problem caused by lazy-loading each task's assignee relationship.

### Fix

The task assignee relationship was eager-loaded in `ProjectController::show()`.

The task query now uses:

```php
->with([
    'assignee',
    'comments.user',
]);
```

Laravel therefore loads the required assignees in a single query using a `WHERE IN` condition instead of performing one query for every task.

### After Optimization

The same board containing 20 tasks produced:

```text
9 total database queries
```

The 20 individual assignee queries were replaced with one eager-loading query:

```sql
select * from "users" where "users"."id" in (...)
```

The measured result was:

```text
Before: 28 queries
After:   9 queries

Before: 20 individual assignee queries
After:   1 eager-loading query
```

This reduced the total query count by 19 queries, approximately 68%.

A regression test was added to the existing:

```text
tests/Feature/TaskTest.php
```

to ensure the board does not return to the N+1 query pattern.

## 🗂️ Database Indexes

Indexes were added based on the actual filtering and sorting requirements instead of indexing every database column.

### Existing Task Indexes

The `tasks` table already contains:

```php
$table->index(['project_id', 'board_column_id']);
$table->index(['assignee_id']);
```

The composite index:

```text
project_id + board_column_id
```

supports task queries involving projects and their board columns.

The `assignee_id` index supports filtering tasks by assigned user.

These indexes were retained and duplicate indexes were not added.

### Due-Date Index

Task 8 introduced due-date filtering and sorting, so an index was added to:

```text
tasks.due_date
```

The migration adds:

```php
$table->index('due_date');
```

This supports queries involving:

```text
due_from
due_to
due_date sorting
```

The index can be removed during rollback using:

```php
$table->dropIndex(['due_date']);
```

### Why Other Indexes Were Not Added

A separate `project_id` index was not added because `project_id` is already the leftmost column of:

```text
(project_id, board_column_id)
```

A duplicate `assignee_id` index was not added because one already exists.

A standard B-tree index was not added to `title` or `description` because the current search implementation uses:

```sql
LIKE '%search%'
```

A leading wildcard generally prevents a normal B-tree index from being useful for this type of contains-search.

If search requirements grow significantly in the future, a dedicated full-text search solution could be considered.

## 📊 Performance Validation

A dedicated performance test was added to the existing:

```text
tests/Feature/TaskTest.php
```

The test measured the task board with a large number of tasks.

The measured result was:

```text
Board load time: 251.41 ms
Board query count: 9
```

The performance test passed with:

```text
2 assertions
```

The board therefore completed the measured performance test with:

```text
9 database queries
251.41 ms measured load time
```

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

```text
tests/Feature/TaskTest.php
```

### Run the Complete Test Suite

```bash
php artisan test
```

### Run Task Tests

```bash
php artisan test tests/Feature/TaskTest.php
```

### Run the Performance Test

```bash
php artisan test tests/Feature/TaskTest.php --filter="task board remains efficient with a large number of tasks"
```

Expected performance output:

```text
Board load time: 251.41 ms
Board query count: 9
```

### Run the Large Dataset Seeder Test

```bash
php artisan test tests/Feature/LargeDatasetSeederTest.php
```

### Run Due-Date Reminder Tests

```bash
php artisan test tests/Feature/SendDueDateReminderJobTest.php
php artisan test tests/Feature/SendDueDateRemindersCommandTest.php
```

### Run Comment Tests

```bash
php artisan test --filter="CommentTest"
```

### Run Notification Tests

```bash
php artisan test --filter="NotificationTest"
```

## 📂 Project Structure

```text
app/
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
└── Feature/
    ├── AuthenticationTest.php
    ├── BoardColumnTest.php
    ├── CommentTest.php
    ├── NotificationTest.php
    ├── ProjectTest.php
    ├── TaskMoveTest.php
    ├── TaskTest.php
    ├── LargeDatasetSeederTest.php
    ├── SendDueDateReminderJobTest.php
    └── SendDueDateRemindersCommandTest.php
```

## 🛣️ Main Web Routes

### Projects

```http
GET     /projects
GET     /projects/create
POST    /projects
GET     /projects/{project}
GET     /projects/{project}/edit
PUT     /projects/{project}
DELETE  /projects/{project}
```

### Board Columns

```http
POST    /projects/{project}/columns
PUT     /projects/{project}/columns/{boardColumn}
DELETE  /projects/{project}/columns/{boardColumn}
```

### Tasks

```http
POST    /projects/{project}/tasks
PUT     /projects/{project}/tasks/{task}
DELETE  /projects/{project}/tasks/{task}
PATCH   /projects/{project}/tasks/{task}/move
```

### Comments

```http
POST    /tasks/{task}/comments
DELETE  /comments/{comment}
```

### Notifications

```http
GET     /notifications
POST    /notifications/{notification}/read
```

## ⚙️ Useful Artisan Commands

```bash
php artisan serve
php artisan migrate
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan db:seed --class=LargeDatasetSeeder
php artisan route:list
php artisan test
php artisan optimize:clear
```

### Queue Commands

```bash
php artisan queue:work
php artisan queue:work --once
php artisan queue:failed
php artisan queue:retry <id>
php artisan queue:forget <id>
```

### Reminder Commands

```bash
php artisan tasks:send-due-date-reminders
php artisan schedule:list
```

## 🧹 Clearing Laravel Caches

If changes to routes, configuration, views, or application code are not appearing correctly:

```bash
php artisan optimize:clear
```

## 🗃️ Database Reset

Reset the database:

```bash
php artisan migrate:fresh
```

Reset and seed:

```bash
php artisan migrate:fresh --seed
```

Reset and generate the large performance dataset:

```bash
php artisan migrate:fresh
php artisan db:seed --class=LargeDatasetSeeder
```

## 🔀 Git Workflow

Development is organized into feature branches.

Create a feature branch:

```bash
git checkout -b feature/task8
```

Commit changes:

```bash
git add .
git commit -m "your commit message"
```

Push the branch:

```bash
git push -u origin feature/task8
```

Then create a Pull Request targeting `main`.

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

## 📈 Task 8 Results

### Search & Filtering

The board supports combined:

```text
Search
+
Assignee
+
Due Date Range
+
Board Column
+
Due Date Sorting
```

### Large Dataset

```text
5 companies
50 users
25 projects
75 board columns
25,000 tasks
```

### N+1 Optimization

```text
Before: 28 queries
After:   9 queries

Before: 20 individual assignee queries
After:   1 eager-loading query
```

Query reduction:

```text
19 fewer queries
≈68% reduction
```

### Board Performance

```text
1,000 tasks
9 database queries
251.41 ms measured board load time
```

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

```text
.env
/node_modules
/vendor
/public/build
/storage/*.key
```

## 🔄 Application Workflow

```text
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
```

## 🔄 Event-Driven Architecture

```text
TaskCreated
TaskMoved
TaskAssigned
CommentAdded
      ↓
Event Listeners
      ↓
Activities / Notifications
```

This keeps secondary behavior such as activity logging and notifications separate from the main task operations.

## 🧵 Background Processing Architecture

```text
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
```

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