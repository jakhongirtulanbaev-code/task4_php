## Task 4 – PHP User Management App

This project is a simple PHP + MySQL user management application that fulfils the requirements from **Task #4_PHP**:

- Registration with e‑mail and password (any non‑empty password is allowed).
- Asynchronous e‑mail confirmation link that activates user account (`unverified` → `active`).
- Login/logout.
- Authenticated, non‑blocked users can see the **user management table** and manage **all** users (including themselves).
- Users can **block**, **unblock**, and **delete** themselves or any other user using a toolbar above the table.
- Multiple selection with checkboxes (including a “select all” checkbox in the header).
- Data in the table is **sorted by last login time** (descending).
- Deleted users are physically removed from the DB (not just “marked”).
- Before each request (except registration/login/confirm), server checks that the session user exists and is **not blocked**.

The UI uses **Bootstrap 5** for styling.

---

### 1. Requirements

- PHP 8.1+ (CLI + web server).
- MySQL 8+ or MariaDB 10.4+.
- Composer is *optional* (no external PHP libraries are required).

---

### 2. Installation

1. **Create database and schema**

   - Create a database (for example `task4_php`).
   - Import `database.sql` from this repository:

   ```bash
   mysql -u root -p task4_php < database.sql
   ```

2. **Configure database connection**

   - Copy `config.example.php` to `config.php` and adjust credentials:

   ```php
   <?php
   return [
       'db_host' => '127.0.0.1',
       'db_name' => 'task4_php',
       'db_user' => 'root',
       'db_pass' => '',
       'base_url' => 'http://localhost/task4', // change for your hosting
       'mail_from' => 'no-reply@example.com'
   ];
   ```

3. **Run the app locally**

   - Place the project into your web root or run PHP’s built‑in server:

   ```bash
   php -S localhost:8000
   ```

   - Open `http://localhost:8000` in the browser.

---

### 3. Main Pages

- `index.php` – router that redirects to login or users table depending on session.
- `register.php` – registration form.
- `login.php` – login form.
- `logout.php` – logout endpoint.
- `confirm.php` – handles e‑mail confirmation link.
- `users.php` – user management table and toolbar (requires authenticated, non‑blocked user).
- `actions.php` – POST handler for block/unblock/delete operations.

---

### 4. Demonstrating the DB Index

The schema in `database.sql` includes:

- **Unique index** on `users.email`.
- Additional index on `status` and `last_login_at` (for faster sorting/filtering).

To show the index in your recording, you can run (for example in MySQL CLI):

```sql
SHOW INDEXES FROM users;
```

---

### 5. Deployment

You can deploy to any shared hosting or PaaS (Render, Railway, VPS, etc.) that supports:

- PHP + MySQL.
- Sending e‑mails via `mail()` or SMTP (configure according to your host).

Make sure to:

- Update `base_url` and mail settings in `config.php`.
- Import `database.sql` in your production DB.

---

### 6. What to Record for Submission

As required in the task description, record a **video without narration** showing:

1. Registration of a new user.
2. Receiving and opening the confirmation link (status becomes `active`).
3. Login as that user.
4. Selecting a **different** user in the table and blocking them (status update visible).
5. Unblocking that user.
6. Selecting **all** users (including current) and blocking them – you should be redirected to login page because the current user becomes blocked.
7. `SHOW INDEXES FROM users;` (or similar UI in your DB client) to demonstrate the unique index.


