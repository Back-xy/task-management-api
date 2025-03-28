# Task Management API – Laravel Backend Assignment

This is a Laravel-based Task Management API built as part of Fastlink's Backend Assignment. It covers user roles, task lifecycle, background job processing, WebSocket alerts, and more.

---

## Features Implemented

✅ Token-based authentication  
✅ Role-based access: Product Owner, Developer, Tester  
✅ Create/update/delete users via API or Artisan command  
✅ Task lifecycle management with multiple statuses  
✅ Subtask & parent task support  
✅ Task logs for status/title/description changes  
✅ Automated re-assignments based on status  
✅ Task import via CSV using queued jobs  
✅ Task export to CSV  
✅ Job progress tracking API  
✅ WebSocket alert to Product Owner when task is overdue  
✅ Email notification when a task is assigned  
✅ Task list and detail APIs with filtering and searching  
✅ Postman Collection provided  

---

## Tech Stack

- **Framework:** Laravel 11+
- **Database:** MySQL
- **Broadcasting:** Laravel Reverb (WebSocket)
- **Mail:** Mailtrap (or any SMTP)
- **Queues:** Database driver
- **Frontend Test:** Vite + Echo (optional)

---

## Installation

### 1. Clone the Repository
```bash
git clone https://github.com/back-xy/task-management-api.git
cd task-management-api
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run build
```

### 3. Create `.env` File
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Set Up `.env` Variables
Update these in your `.env` file:

#### Database
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

#### Mail (example using Mailtrap)
```
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=task@app.com
MAIL_FROM_NAME="Task Management"
```

#### WebSockets (Reverb)
```
BROADCAST_DRIVER=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="http"
```

### 5. Migrate and Seed
```bash
php artisan migrate --seed
```

This will create some fake users and tasks.

---

##  Run the Project

### 1. Start the Laravel App
```bash
php artisan serve
```

### 2. Start the Queue Worker (for emails, import, etc.)
```bash
php artisan queue:work
```

### 3. Start WebSocket Server (Reverb)
```bash
php artisan reverb:start
```

### 4. Start the Laravel Scheduler
```bash
php artisan schedule:work
```

---

## Artisan Commands

### Export Tasks to CSV
```bash
php artisan export:tasks
```

CSV will be saved in `storage/app/exports/`.

---

### Import Tasks from CSV
```bash
php artisan import:tasks {relative_path_to_file}
```

Example:
```bash
php artisan import:tasks imports/tasks.csv
```

Then track the progress using API:
```
GET /api/import-status/{id}
```

---

### Create Users via Artisan
```bash
php artisan user:create
```

---

## Email Notification

Emails are sent in the background when tasks are assigned. Make sure Mailtrap credentials in `.env` are correct and `php artisan queue:work` is running.

---

## WebSocket Alerts

When a task passes its due date, the product owner will receive a WebSocket event.

> Product owner must be listening to a private channel like: `task-due.{userId}`

Broadcast events:
- `App\Events\TaskOverdue`

Scheduled every 10 seconds using:
```php
Schedule::command('tasks:check-overdue')->everyTenSeconds();
```

Below is an updated documentation snippet that explains how the WebSocket alerts work and how testers can verify them using the frontend route we created:

---

## WebSocket Alerts

When a task passes its due date, the product owner will receive a WebSocket event. The event is broadcast via:

- **Event Class:** `App\Events\TaskOverdue`

The product owner must be listening on a private channel (e.g., `user.{userId}`) to receive these events.

The scheduled command that checks for overdue tasks and triggers the event is set up like this:

```php
Schedule::command('tasks:check-overdue')->everyTenSeconds();
```

### Testing the WebSocket Alert via Frontend

To test the WebSocket alert functionality:

1. **Login:**  
   Use the Login page at `/login` to authenticate as the Product Owner.  
   Upon successful login, the user is redirected to the Test Alert page.

2. **Test Alert Page:**  
   This page establishes a WebSocket connection using Reverb and listens on the private channel (`user.{userId}`).

3. **Trigger an Alert:**  
   When a task passes its due date (and is not marked as DONE or REJECTED), the scheduled command will broadcast a `TaskOverdue` event. The Test Alert page will immediately display the event details (in JSON format).

4. **Sign Out:**  
   Use the Sign Out button on the Test Alert page to clear your session and return to the login page, or test with another user.

This setup allows you to verify the complete flow—from checking overdue tasks, broadcasting the event, to receiving the alert in real time on the frontend.

---

---

## Testing API

Use Postman to test all API endpoints.  
✅ A complete collection is provided: `Task-Management-API-Documentation.postman_collection.json` in the root folder.

---

## ❌ Not Implemented Yet

- No unit/feature tests  
- No Docker  
- No live deployment  

---

## ✅ Project Completed!

Everything listed in the assignment has been implemented and tested.

Let me know if you need help turning this into a Dockerized or deployed solution later!