# Agent Instructions for Abadia Agentes App

This document provides instructions and notes for developers (or agents like Jules) working on this CodeIgniter 3 application.

## Initial Setup

1.  **Database Configuration**:
    *   Ensure your database is configured in `application/config/database.php`.
    *   The application expects a database named `afiliacion` (or as configured).

2.  **Run SQL Schemas**:
    *   Execute `application/sql/ion_auth.sql` to set up tables for user authentication (Ion Auth).
        *   Default admin: `admin@admin.com` / `password`
    *   Execute `application/sql/agentes_schema.sql` to set up tables for the Agentes CRUD functionality.
    *   **Important**: Populate the lookup tables (`estados`, `ciudades`, `municipios`, `parroquias`) in `agentes_schema.sql` with comprehensive data for Venezuela. The provided sample data is minimal.

3.  **CodeIgniter Configuration**:
    *   Set a strong, unique **encryption key** in `application/config/config.php`:
        `$config['encryption_key'] = 'YOUR_VERY_STRONG_RANDOM_KEY_HERE';`
    *   The `base_url` in `application/config/config.php` is currently set for HTTPS: `https://localhost/abadia/`. Adjust if necessary.
    *   Ensure your web server (e.g., Apache) has `mod_rewrite` enabled for the `.htaccess` rules to work.

4.  **Ion Auth Configuration**:
    *   Set a unique **session hash** in `application/config/ion_auth.php`:
        `$config['session_hash'] = 'YOUR_UNIQUE_RANDOM_SESSION_HASH_HERE';`
    *   Review other settings in `application/config/ion_auth.php` (e.g., email settings, site title) and adjust as needed. If email functionality (activation, forgotten password) is required, configure CodeIgniter's email settings (`application/config/email.php`) and set `$config['use_ci_email'] = TRUE;` in `ion_auth.php`.

5.  **File Uploads**:
    *   The application uploads agent profile pictures to `./uploads/agentes_fotos/`. Ensure this directory exists and is writable by the web server. The application attempts to create it with `0755` permissions if it's missing.
    *   A default avatar is referenced at `uploads/default_avatar.png`. Please ensure this image exists at the root of your `uploads` directory (i.e., `./uploads/default_avatar.png` relative to the CI project root) or update the paths in the views (`agentes/create.php`, `agentes/edit.php`, and `agentes/index.php` DataTables JS) if you place it elsewhere.

6.  **CSRF Protection (Recommended)**:
    *   CSRF protection is currently disabled (`$config['csrf_protection'] = FALSE;` in `application/config/config.php`).
    *   For production environments, it is strongly recommended to enable CSRF protection.
    *   If enabled:
        *   Ensure all forms use `form_open()` or manually include the CSRF token.
        *   Update all AJAX POST requests (e.g., in dependent dropdown JavaScript, DataTables) to include the CSRF token name and hash. You can pass the token name and hash to your JavaScript via PHP variables.

## Development Notes

*   **Datatables**: Server-side processing for the agents list is implemented manually in `Agentes::get_agentes_list_ss()`. No external DataTables library file is currently used.
*   **jQuery Validation**: Basic client-side validation is implemented in `agentes/create.php` and `agentes/edit.php`. For more robust validation, consider using the jQuery Validation Plugin. Server-side validation in the `Agentes` controller is authoritative.
*   **Dependent Dropdowns**: Logic for Estado, Ciudad, Municipio, Parroquia dropdowns is handled via jQuery AJAX calls in `agentes/create.php` and `agentes/edit.php`, targeting methods in the `Agentes` controller.

## Coding Conventions

*   Follow CodeIgniter 3 coding standards.
*   Comment code where necessary for clarity.
*   Ensure HTML output is properly escaped to prevent XSS.
*   Validate and sanitize all user input.
```
