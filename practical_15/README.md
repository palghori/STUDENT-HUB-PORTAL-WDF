# 🎓 StudentHub Portal - Practical 15 Final Integration & Deployment Manual

Welcome to the **StudentHub Portal**, an end-to-end full-stack campus management web application developed for the Web Development Framework (WDF) laboratory curriculum.

This project represents the final integration of all foundational practical exercises into a single, cohesive, responsive, and secure web application.

---

## 📌 Table of Contents
1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Repository Structure](#repository-structure)
4. [Prerequisites & Local Installation](#prerequisites--local-installation)
5. [Database Setup](#database-setup)
6. [Apache Virtual Host Configuration](#apache-virtual-host-configuration)
7. [Default Test Accounts](#default-test-accounts)
8. [Module Walkthrough & End-to-End Workflow](#module-walkthrough--end-to-end-workflow)
9. [Security & RBAC Architecture](#security--rbac-architecture)
10. [GitHub Submission Steps](#github-submission-steps)

---

## 1. Project Overview

The StudentHub Portal provides students and academic administrators with a centralized digital campus environment. Key capabilities include:
- **Student Management (CRUD)**: Search by student name/email, course filtering, add/edit/delete records.
- **Event Management**: Campus event scheduling, image poster upload with MIME type and size validation, and thumbnail rendering.
- **Asynchronous Operations (SPA)**: Zero-reload event management using PHP JSON API endpoints and the JavaScript Fetch API.
- **Role-Based Access Control (RBAC)**: Distinct permissions for `admin` and `student` roles with server-side protection on administrative routes.
- **Dynamic Menus**: Navigation bars that automatically adapt options based on the authenticated user's session role.
- **Security Audit Logging**: Persistent database records of all system events, authentication attempts, and administrative modifications.

---

## 2. Technology Stack

- **Frontend**: HTML5, Semantic Elements, Vanilla CSS3 (Flexbox/Grid, Warm Palette), JavaScript (ES6+, Fetch API, DOM Manipulation).
- **Backend**: PHP 8.x (Procedural style, Session management, Bcrypt hashing).
- **Database**: MySQL / MariaDB (Procedural MySQLi, Foreign Keys, Aggregate queries, SQL JOINs).
- **Server Environment**: Apache HTTP Server via XAMPP.

---

## 3. Repository Structure

```
practical 15/
├── CSS/
│   └── style.css                 # Master responsive stylesheet
├── DATA/
│   └── studenthub_final.sql      # Master database schema and seed data
├── PAGES/
│   ├── db_connect.php            # MySQLi database connection
│   ├── audit_helper.php          # Audit logging helper function
│   ├── navbar.php                # Role-based dynamic navigation bar
│   ├── index.php                 # Integrated portal homepage
│   ├── login.php                 # Secure login with 1-click test buttons
│   ├── register.php              # Student registration with password hashing
│   ├── logout.php                # Session destruction and audit logging
│   ├── dashboard.php             # Student dashboard & registration history
│   ├── admin_dashboard.php       # Admin dashboard with dynamic metrics
│   ├── students.php              # Student CRUD with search & filter
│   ├── add_student.php           # Admin student creation form
│   ├── edit_student.php          # Admin student edit form
│   ├── events.php                # Event management with poster thumbnails
│   ├── add_event.php             # Event creation with image file validation
│   ├── edit_event.php            # Event editing with optional poster replacement
│   ├── ajax_events.php           # Fetch API CRUD (no page reload)
│   ├── api.php                   # RESTful PHP JSON API endpoint
│   ├── audit_logs.php            # Security audit log viewer & filter
│   └── access_denied.php         # HTTP 403 Forbidden RBAC block screen
├── UPLOADS/                      # Directory for uploaded event posters
├── vhost_example.conf            # Apache virtual host configuration file
├── TESTING_CHECKLIST.md          # Complete testing & verification checklist
└── README.md                     # Comprehensive project documentation
```

---

## 4. Prerequisites & Local Installation

1. **Install XAMPP**: Ensure XAMPP is installed on your workstation (macOS, Windows, or Linux) with **Apache** and **MySQL** services running.
2. **Clone or Copy Repository**:
   Copy the project into your local XAMPP `htdocs` directory:
   - **macOS**: `/Applications/XAMPP/xamppfiles/htdocs/STUDENT HUB PORTAL WDF/`
   - **Windows**: `C:\xampp\htdocs\STUDENT HUB PORTAL WDF\`

---

## 5. Database Setup

1. Open **phpMyAdmin** in your browser: `http://localhost/phpmyadmin/`.
2. Click on the **Import** tab.
3. Browse to and choose the file: `practical 15/DATA/studenthub_final.sql`.
4. Click **Import** (or **Go**).
5. The script creates the `studenthub_db` database along with tables `students`, `events`, `registrations`, and `audit_logs`, populated with initial seed records.

---

## 6. Apache Virtual Host Configuration

To run the portal using a custom local domain (e.g. `http://studenthub.local`):

### Step 1: Edit Apache Virtual Hosts
Open your XAMPP Apache virtual hosts configuration file:
- **macOS**: `/Applications/XAMPP/xamppfiles/etc/extra/httpd-vhosts.conf`
- **Windows**: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`

Append the following block:
```apache
<VirtualHost *:80>
    ServerAdmin admin@studenthub.com
    DocumentRoot "/Applications/XAMPP/xamppfiles/htdocs/STUDENT HUB PORTAL WDF/practical 15/PAGES"
    ServerName studenthub.local
    ServerAlias www.studenthub.local

    <Directory "/Applications/XAMPP/xamppfiles/htdocs/STUDENT HUB PORTAL WDF/practical 15/PAGES">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/studenthub-error_log"
    CustomLog "logs/studenthub-access_log" common
</VirtualHost>
```

### Step 2: Update System Hosts File
Open your system hosts file with administrative privileges:
- **macOS/Linux**: `/etc/hosts`
- **Windows**: `C:\Windows\System32\drivers\etc\hosts`

Add this line:
```
127.0.0.1   studenthub.local www.studenthub.local
```

### Step 3: Restart Apache
Restart the Apache service inside the XAMPP Control Panel. You can now access the portal directly at:
👉 **`http://studenthub.local/index.php`**

*(Note: Standard localhost access is also always available at `http://localhost/STUDENT%20HUB%20PORTAL%20WDF/practical%2015/PAGES/index.php`)*

---

## 7. Default Test Accounts

| Role | Email | Password | Access Capabilities |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@studenthub.com` | `admin123` | Full access to Admin Dashboard, Students, Events, Audit Logs |
| **Student** | `aarav@studenthub.com` | `student123` | Student Dashboard, Event Enrollment, Profile |
| **Student** | `diya@studenthub.com` | `student123` | Student Dashboard, Event Enrollment, Profile |

> 💡 **Quick Testing Tip**: On the `login.php` page, click either **"Log In as Admin"** or **"Log In as Student"** for instant 1-click test evaluation without typing!

---

## 8. Module Walkthrough & End-to-End Workflow

1. **Homepage (`index.php`)**: View live KPI statistics, system modules, and active campus announcements.
2. **Registration (`register.php`)**: Register a new student account. Password is encrypted using `password_hash()`.
3. **Login (`login.php`)**: Authenticate securely. The system checks `password_verify()` and redirects admins to `admin_dashboard.php` and students to `dashboard.php`.
4. **Student Portal (`dashboard.php`)**: Browse upcoming events, click "Enroll Now" to register, and view enrollment history.
5. **RBAC Security Test**: While in the student portal, click the red button **"Try Accessing Admin Dashboard"** to verify that non-admin accounts are blocked with a `403 Access Denied` alert.
6. **Admin Dashboard (`admin_dashboard.php`)**: View dynamic statistics loaded via MySQL (`COUNT(*)`), review recent registrations, broadcast campus announcements, and monitor audit events.
7. **Student Management (`students.php`)**: Add, search, filter by course, edit, and delete student records with confirmation prompts.
8. **Event Management (`events.php`)**: Schedule campus events with poster file uploads. Only valid images up to 2MB are accepted.
9. **AJAX Single-Page CRUD (`ajax_events.php`)**: Add, edit, and delete events asynchronously using JavaScript Fetch API without reloading the page.
10. **Audit Trail (`audit_logs.php`)**: Filter and inspect timestamped logs of all logins, logouts, administrative broadcasts, and CRUD operations.
11. **Logout (`logout.php`)**: Invalidate sessions, clear cookies, and write a logout audit record.

---

## 9. Security & RBAC Architecture

- **Session Security**: Initiated with `session_start()`, regenerated with `session_regenerate_id(true)` upon successful login, and completely wiped on logout.
- **SQL Injection Defense**: User variables are sanitized with `mysqli_real_escape_string()` and numeric parameters cast to integers `(int)`.
- **Cross-Site Scripting (XSS) Defense**: Output escaped using `htmlspecialchars()`.
- **File Upload Safeguards**: MIME-type validation via `finfo_file` and extension whitelisting prevents malicious script uploads.
- **Audit Trails**: Real-time logging of authentication events, user role adjustments, and data modifications into `audit_logs`.

---

## 10. GitHub Submission Steps

To submit this repository to GitHub:

```bash
# 1. Navigate to the project root
cd "/Users/palghori/Documents/STUDENT HUB PORTAL WDF"

# 2. Check current status
git status

# 3. Stage all files including practical 15
git add .

# 4. Commit changes with a descriptive message
git commit -m "feat(practical-15): complete final integration, documentation, testing, and deployment"

# 5. Push to remote GitHub repository
git push origin main
```
