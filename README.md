# Abadia Sales & Affiliation App

This is a CodeIgniter 3 application designed to manage sales agents (`agentes`) and affiliation contracts (`afiliaciones`). It features a complete CRUD system for agents, a multi-step affiliation form, user authentication with different roles, and a soft-delete mechanism for data integrity.

## Features

*   **Authentication**: Powered by **Ion Auth**, providing a robust system for login, logout, password management, and user groups.
*   **Agent Management**: Full CRUD (Create, Read, Update, Delete) functionality for sales agents.
    *   Profile picture uploads.
    *   **Soft Deletes**: Deleting an agent moves them to a "Papelera" (Trash) instead of physically deleting them, preserving historical data.
    *   **Access Control**: Only 'admin' and 'leadership' groups can view the Papelera and restore deleted agents.
*   **Affiliation Module**: A multi-step form for creating new affiliation contracts, including a dynamic family group section.
*   **DataTables**: Server-side processing for agent lists ensures high performance with large datasets.
*   **Modern UI**: Built with Bootstrap 5.3 for a responsive and clean user interface.

---

## Server Requirements

*   PHP 7.2 or higher (required for Argon2 password hashing used by Ion Auth).
*   MySQL / MariaDB Database.
*   Web Server (Apache, Nginx) with `mod_rewrite` enabled for URL rewriting.
*   Composer is **not** required for this version.

---

## Installation

You can install the application using the easy web-based installer or by importing the SQL file manually.

### Step 1: Download & Configure

1.  **Download Files**: Place all the application files on your web server.
2.  **Configure `database.php`**:
    *   Open `application/config/database.php`.
    *   Set your database `hostname`, `username`, `password`, and `database` name.
    *   Ensure the database you specify exists and is empty.
3.  **Configure `config.php`**:
    *   Open `application/config/config.php`.
    *   Set your `base_url`. For a local XAMPP setup, it might be `http://localhost/abadia/`. **It must end with a slash `/`**.
    *   Set a long, random `encryption_key`. This is critical for security.
4.  **Configure `ion_auth.php`**:
    *   Open `application/config/ion_auth.php`.
    *   Set a unique, random `session_hash`. This is important for session security.

### Step 2 (Option A): Web-Based Installation (Recommended)

1.  Navigate to `http://your-site.com/install` in your web browser.
2.  You will see the installer page. If your database connection is correct, you can proceed.
3.  Click the **"Instalar Base de Datos Ahora"** button.
4.  The installer will run all the necessary SQL queries to set up your tables.
5.  If successful, you will see a success message.
6.  **IMPORTANT**: For security, **delete the `Install.php` file** from the `application/controllers/` directory after the installation is complete.

### Step 2 (Option B): Manual SQL Installation

1.  Open your database management tool (like phpMyAdmin).
2.  Select your empty database.
3.  Import the `install.sql` file located in the root of the project.
4.  This will create and populate all the necessary tables.

---

## Usage

*   **Admin Login**: After installation, you can log in with the default administrator account:
    *   **Email**: `admin@admin.com`
    *   **Password**: `password`
*   It is highly recommended to change this password immediately after your first login.

---

## External Components & Libraries

This project relies on several excellent open-source frontend libraries delivered via CDN.

| Library       | Version | CDN Link                                                              |
|---------------|---------|-----------------------------------------------------------------------|
| Bootstrap CSS | 5.3.3   | `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css` |
| Bootstrap JS  | 5.3.3   | `https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js` |
| jQuery        | 3.7.1   | `https://code.jquery.com/jquery-3.7.1.min.js`                           |
| DataTables Core | 2.0.8   | `https://cdn.datatables.net/2.0.8/js/dataTables.js`                 |
| DataTables BS5 | 2.0.8  | `https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css`      |
| DataTables BS5 JS | 2.0.8 | `https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js`      |
| SweetAlert2   | 11.x    | `https://cdn.jsdelivr.net/npm/sweetalert2@11`                          |
| Font Awesome  | 5.15.4  | `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css` |
