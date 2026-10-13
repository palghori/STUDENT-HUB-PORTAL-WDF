# 📋 StudentHub Portal - Practical 15 Testing & Verification Checklist

This document provides a systematic verification of all system requirements, security protections, accessibility standards, and end-to-end user workflows for **Practical 15**.

---

## 1. Page Linking & Navigation Integrity Checklist

| Page File | Description | Accessible Roles | Status |
| :--- | :--- | :--- | :---: |
| `index.php` | Main Landing & Overview Portal | Guest, Student, Admin | ✅ Pass |
| `login.php` | Authentication & Demo Quick Logins | Guest | ✅ Pass |
| `register.php` | Student Account Registration Form | Guest | ✅ Pass |
| `logout.php` | Session Invalidation & Audit Logging | Student, Admin | ✅ Pass |
| `dashboard.php` | Student Dashboard & Registration History | Student | ✅ Pass |
| `admin_dashboard.php` | Administrator Dashboard & System Metrics | Admin Only | ✅ Pass |
| `students.php` | Student CRUD with Search & Filter | Admin Only | ✅ Pass |
| `add_student.php` | Admin Add Student Form | Admin Only | ✅ Pass |
| `edit_student.php` | Admin Edit Student Form | Admin Only | ✅ Pass |
| `events.php` | Campus Event Listings with Posters | Guest, Student, Admin | ✅ Pass |
| `add_event.php` | Event Scheduling with Poster Upload | Admin Only | ✅ Pass |
| `edit_event.php` | Event Update with File Replacement | Admin Only | ✅ Pass |
| `ajax_events.php` | Fetch API SPA (No Page Reload) | Guest, Student, Admin | ✅ Pass |
| `api.php` | RESTful PHP JSON API | Backend Endpoint | ✅ Pass |
| `audit_logs.php` | Security Audit Trail & Log Viewer | Admin Only | ✅ Pass |
| `access_denied.php` | 403 Forbidden RBAC Block Screen | Any Unauthorized User | ✅ Pass |

---

## 2. End-to-End Sessions, Roles & Validations Checklist

- [x] **Session Initialization**: `session_start()` executes at the top of every secure script prior to output.
- [x] **Session Fixation Defense**: `session_regenerate_id(true)` called upon successful credential authentication.
- [x] **Role Distinction**: Users are strictly partitioned into `student` and `admin` roles stored in `$_SESSION['role']`.
- [x] **Role Redirection**:
  - `admin` redirected to `admin_dashboard.php` upon login.
  - `student` redirected to `dashboard.php` upon login.
- [x] **RBAC Route Protection**: Non-admin visitors requesting `admin_dashboard.php`, `students.php`, `add_event.php`, or `audit_logs.php` are intercepted and redirected to `access_denied.php`.
- [x] **Input Validation**:
  - Email format validation.
  - Minimum password length requirement (6+ characters).
  - Required fields verified on client and server side.
- [x] **Duplicate Checks**: Prevents multiple accounts sharing the same email address or enrollment ID.

---

## 3. Database Operations (CRUD) Verification

- [x] **SELECT**:
  - Dynamic KPI counts (`COUNT(*)`) for students, admins, events, registrations, and audit logs.
  - Multi-table `INNER JOIN` queries fetching student details alongside enrolled event titles.
  - Search and filter queries using `LIKE '%search%'` and exact matching on course names.
- [x] **INSERT**:
  - New student registration (`students` table).
  - New campus event with poster filename (`events` table).
  - Student event enrollment (`registrations` table).
  - Timestamped action tracking (`audit_logs` table).
- [x] **UPDATE**:
  - Modifying student records (`edit_student.php`).
  - Editing event title, date, category, and replacing poster image (`edit_event.php`).
  - Toggling user roles.
- [x] **DELETE**:
  - Deleting student accounts with JavaScript confirmation modal.
  - Deleting events, with automatic file cleanup of associated poster images via `unlink()`.
  - Truncating audit logs with automatic recreation of the clearing action record.

---

## 4. Security Verification Checklist

- [x] **Password Security**: Passwords hashed using industry-standard `password_hash($pass, PASSWORD_DEFAULT)` using Bcrypt and authenticated via `password_verify()`. Plaintext passwords are never stored.
- [x] **SQL Injection Prevention**: All user input values concatenated into SQL strings are properly escaped using `mysqli_real_escape_string()`, with numeric IDs sanitized via integer casting `(int)$_GET['id']`.
- [x] **Cross-Site Scripting (XSS) Prevention**: All dynamic user-supplied content printed to HTML is wrapped with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`.
- [x] **File Upload Validation**:
  - Extension whitelist check: Only `jpg`, `jpeg`, `png`, and `webp` allowed.
  - MIME-type verification using PHP `finfo_file(FILEINFO_MIME_TYPE)`.
  - File size cap enforced at 2MB (`2 * 1024 * 1024` bytes).
  - Sanitized filenames generated using unique timestamp prefixes (`time() . '_' . $cleaned_name`).
- [x] **Audit Trail**: Every significant administrative and user event (Login, Logout, Failed Login, Add Event, Delete Student, Clear Logs) is recorded in `audit_logs` with client IP and timestamp.

---

## 5. Accessibility & Responsiveness Checklist

- [x] **Semantic HTML5**: Pages structured using semantic `<nav>`, `<main>`, `<section>`, `<article>`, `<header>`, and `<footer>` elements.
- [x] **High-Contrast Typography**: Complies with standard readability guidelines using dark brown/charcoal text on clean white/cream backgrounds.
- [x] **Keyboard Navigation**: Interactive links and buttons feature high-contrast `:focus` outlines (`outline: 3px solid #d35400`) for keyboard tab navigation.
- [x] **Media Alt Attributes**: All images, including dynamic event poster thumbnails, include descriptive `alt` tags.
- [x] **Responsive Viewport**: Viewport meta tag included on all pages (`width=device-width, initial-scale=1.0`); navigation gracefully wraps on mobile displays.

---

## 6. AJAX & Fetch API Verification

- [x] **JSON Response Header**: `api.php` sets `header("Content-Type: application/json")`.
- [x] **HTTP Verbs**: Correctly dispatches `GET`, `POST`, `PUT`, and `DELETE` requests.
- [x] **Zero Page Reload**: DOM dynamically updates when events are added, edited, or removed without refreshing the browser window.
- [x] **Visual State Indicators**: Loading spinner displayed while asynchronous requests are pending; green/red banner alerts appear upon completion.
