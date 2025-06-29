# Prevcrim

Prevcrim is a simple PHP/MySQL application for managing crime related data. This
repository contains the source code together with a small PHPUnit test suite.

## Requirements

* PHP 8.1 or greater
* Composer
* MySQL (or compatible MariaDB) server

## Installation

1. Clone the repository and install PHP dependencies:

   ```bash
   composer install
   ```

2. Create an empty MySQL database and user. Import `schema.sql` to create all
   the tables and seed the catalog of `tipo_delito`:

   ```bash
   mysql -u <user> -p < schema.sql
   ```

3. Configure the following environment variables so the application can connect
   to the database:

   - `DB_HOST` – database host (for example `localhost`)
   - `DB_NAME` – name of the MySQL database
   - `DB_USER` – database user
   - `DB_PASS` – database password

   These can be exported in your shell or written to a small `.env` file that is
   loaded before running PHP.

## Usage

Run the built‑in PHP web server from the project root and browse to
`http://localhost:8000` to access the login screen:

```bash
php -S localhost:8000
```

After logging in you will be taken to the dashboard. Administrators can create,
edit and delete users from the `admin/` section.

## Running Tests

A small PHPUnit suite is provided covering the login procedure and basic CRUD
operations on the `usuario` table. The tests use an isolated SQLite database so
no real data is modified.

Ensure that the PHP SQLite extension is available (on Debian/Ubuntu install the
`php-sqlite3` package) before running the suite.

Execute the tests with:

```bash
vendor/bin/phpunit
```

