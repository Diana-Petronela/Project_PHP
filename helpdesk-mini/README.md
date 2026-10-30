# HelpDesk Mini

A small IT ticketing app built with plain, procedural PHP 8 (no framework),
MySQL via PDO (prepared statements, no ORM), hand-written HTML/CSS, and
vanilla JavaScript (fetch API). Built as a portfolio project for a Junior
Full Stack Web Developer interview.

## Features

- Register / log in / log out (sessions, `password_hash` / `password_verify`)
- Create, list, edit, delete tickets (every user only sees their own)
- Ticket detail page
- Change a ticket's status (Open / In Progress / Closed) via `fetch`,
  without reloading the page

## Requirements

- XAMPP (or any PHP 8 + MySQL setup)

## Setup

1. Start Apache and MySQL in the XAMPP control panel (MySQL is required;
   Apache is optional, see "Running the app" below).
2. Import the database:
   ```
   mysql -u root < schema.sql
   mysql -u root < seed.sql   # optional demo data
   ```
3. Check `config.php` matches your MySQL setup (defaults: host 127.0.0.1,
   user root, no password - XAMPP's defaults).

## Running the app

Simplest option, no Apache/htdocs needed - PHP's built-in dev server,
run from inside this folder:

```
C:\xampp\php\php.exe -S localhost:8000
```

Then open `http://localhost:8000/login.php`.

## Demo account

- Email: `demo@helpdesk.local`
- Password: `password123`

## Project structure

```
config.php          database connection settings
db.php               PDO connection (get_db())
helpers.php          small shared helpers (h(), require_login(), ...)
register.php         sign up
login.php            log in
logout.php           log out
index.php            ticket list
ticket_new.php        create ticket
ticket_edit.php       edit ticket
ticket_delete.php    delete ticket
ticket_view.php      ticket detail page
status_update.php    JSON endpoint used by fetch to change status
partials/nav.php     shared top navigation bar
assets/style.css     styling
assets/app.js        fetch logic for the status dropdown
schema.sql, seed.sql  database setup
```

## Security notes

- All SQL goes through PDO prepared statements.
- All user-provided text is escaped with `htmlspecialchars()` before
  being printed (XSS protection).
- Passwords are hashed with `password_hash()` (bcrypt), never stored
  in plain text.
- Every ticket query filters by `user_id`, not just by the ticket's `id`,
  so one user cannot read, edit or delete another user's ticket by
  guessing its id in the URL.
- `session_regenerate_id()` is called on login to prevent session fixation.

## Possible improvements

- CSRF tokens on the forms
- Roles (user/agent), comments, pagination (deliberately left out of
  this "mini" version to keep the codebase small and easy to explain)
