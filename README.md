# Mini Ticket Tracker

A small support-ticket tracker: a REST API, a MySQL database and a single-page UI.
You can create tickets, list them (filter by status, paginated), change a ticket's status, and see how many tickets are in each status.

| Part     | Stack                         | Folder      |
|----------|-------------------------------|-------------|
| API      | Laravel 13 (PHP 8.3+)         | `backend/`  |
| Database | MySQL 8                       | —           |
| UI       | Angular 18 + Bootstrap 5      | `frontend/` |

---

## Backend

**Requires:** PHP 8.3+ (with `pdo_mysql`), Composer, MySQL 8.

### Setup

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Create two databases — one for the app and one for the tests:

```sql
CREATE DATABASE mini_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE mini_tracker_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Open `backend/.env` and set your MySQL username and password (`DB_USERNAME`, `DB_PASSWORD`). Then create the table and 5 sample tickets:

```bash
php artisan migrate --seed
```

### Run

```bash
php artisan serve            # API on http://127.0.0.1:8000
```

### API

| Method  | Endpoint          | Description                                                        |
|---------|-------------------|--------------------------------------------------------------------|
| `POST`  | `/tickets`        | Create a ticket. `title` required; `description`, `priority` optional |
| `GET`   | `/tickets`        | List tickets, newest first. Query: `?status=&page=&limit=` (limit 1–100, default 10) |
| `PATCH` | `/tickets/{id}`   | Update a ticket's `status` and/or `priority`                        |
| `GET`   | `/tickets/stats`  | Number of tickets in each status, plus the total                   |

Every response has the same shape:

```json
{ "status": { "code": 1, "message": "Success" }, "data": { } }
```

| `status.code` | Meaning                                         |
|---------------|-------------------------------------------------|
| 1             | Success                                         |
| 2             | Failed (unexpected server error)                |
| 3             | Validation failed — `message` lists the errors  |
| 4             | Record not found                                |

Example — `GET /tickets/stats`:

```json
{
  "status": { "code": 1, "message": "Success" },
  "data": { "by_status": { "open": 3, "in_progress": 1, "closed": 1 }, "total": 5 }
}
```

### Tests

```bash
php artisan test
```

The tests call each API endpoint and check the response and the database. They run against the `mini_tracker_test` database, which is reset before every test, so your real data is never touched.

---

## Frontend

**Requires:** Node.js 20 and npm. The backend must be running.

### Setup

In a second terminal:

```bash
cd frontend
npm install
```

### Run

```bash
npm start                    # UI on http://localhost:4200
```

The UI calls the API at `http://127.0.0.1:8000` (set in `frontend/src/environments/environment.ts`).
The API only accepts browser requests from `http://localhost:4200` (`FRONTEND_URL` in `backend/.env`).

### Tests

There are no frontend tests yet — see "Decisions & Tradeoffs" below.

---

## Decisions & Tradeoffs

**Enums for status and priority.** Invalid values are rejected before they reach the database.

Shortcuts taken, and how I would improve them:

- **No login.** Anyone can create or change any ticket. Add login (Laravel Sanctum) and record `created_by` / `updated_by`, so we know who created a ticket and who changed its status.
- **No notifications.** Nobody is told when a ticket is created. Email the support team, sent through a queue so the API stays fast.
- **No history.** Only the current status is kept. Record every change (old → new status, who, when).
- **Raw error messages.** Unexpected server errors return the error text. Log the error and return "Something went wrong".
- **No frontend tests.** Only the API is tested. Add frontend tests and run all tests on every push with GitHub Actions.

### Assumptions

- **Default priority is `medium`** when none is given — the brief does not specify one. New tickets start as `open`.
- **`PATCH /tickets/{id}` only changes `status` and `priority`**, as the brief describes; other fields such as `title` are ignored.
- **Endpoints are served at `/tickets`** (no `/api` prefix) to match the brief exactly.
