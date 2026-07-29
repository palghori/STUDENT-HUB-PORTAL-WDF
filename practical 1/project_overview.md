# StudentHub Project Overview

This document contains the Sitemap, Project Folder Structure, and Low-Fidelity Wireframes for the StudentHub portal.

---

## 🗺️ 1. Sitemap

The sitemap shows the navigation flow and hierarchy of the StudentHub portal.
```
    Home[index.html<br>Landing Page] --> Login[login.html]
    Home --> Register[register.html]
    Home --> Nav[Main Navigation]
    
    Nav --> Dashboard[dashboard.html]
    Nav --> Courses[courses.html]
    Nav --> Assignments[assignment.html]
    Nav --> Attendance[attendence.html]
    Nav --> Result[result.html]
    Nav --> Profile[profile.html]
    Nav --> Contact[contact.html]
```

---

## 📁 2. Project Folder Structure

This represents the organization of your codebase.

```text
StudentHub/
│
├── CSS/
│   └── script.css            # Main stylesheet for all pages
│
├── DOCS/                     # Documentation files (e.g., this file, requirements)
│
├── IMAGES/                   # Project images and assets
│
├── JS/
│   ├── basic.html            # Basic JS testing/template file
│   └── script.js             # Main JavaScript logic for interactivity
│
├── PAGES/
│   ├── index.html            # Landing page
│   ├── dashboard.html        # Main student dashboard
│   ├── courses.html          # Course listing page
│   ├── assignment.html       # Assignments page
│   ├── attendence.html       # Attendance tracking page (Note: spelled 'attendence')
│   ├── result.html           # Grades and results page
│   ├── profile.html          # User profile page
│   ├── contact.html          # Support/Contact page
│   ├── login.html            # Authentication - Login
│   └── register.html         # Authentication - Registration
│
├── README.md                 # Project introduction and setup instructions
└── requirement.docx          # Original project requirements document
```

---

## 🖼️ 3. Low-Fidelity Wireframes

These text-based wireframes represent the general layout and structure of the key pages.

### A. Landing Page (`index.html`)

```text
+-----------------------------------------------------------------+
|  [StudentHub] (Brand Logo)                                      |
+-----------------------------------------------------------------+
|                                                                 |
|                      Welcome to StudentHub                      |
|      StudentHub is your simple student portal for checking      |
|    classes, assignments, results, and profile information...    |
|                                                                 |
|   +----------------+   +----------------+   +----------------+  |
|   |   Dashboard    |   |    Courses     |   |    Support     |  |
|   | (Description)  |   | (Description)  |   | (Description)  |  |
|   +----------------+   +----------------+   +----------------+  |
|                                                                 |
|  [Dashboard] | [Courses] | [Assignments] | [Attendance] |       |
|  [Result] | [Profile] | [Contact] | [Login] | [Register]        |
|                                                                 |
+-----------------------------------------------------------------+
```

### B. Dashboard Page (`dashboard.html`)

```text
+-----------------------------------------------------------------+
|  [StudentHub]   Home | Dashboard | Courses | Assignments ...    |
+-----------------------------------------------------------------+
|                                                                 |
|                      Welcome back, Ansh                         |
|               Your classes and notices are ready.               |
|                                                                 |
|   +------------------+  +------------------+ +----------------+ |
|   | Upcoming Class   |  | Pending Task     | | Attendance     | |
|   | WDF at 10:00 AM  |  | HTML Project due | | 85% this month | |
|   +------------------+  +------------------+ +----------------+ |
|                                                                 |
+-----------------------------------------------------------------+
```

### C. Standard Content Page (e.g., `contact.html`, `login.html`)

```text
+-----------------------------------------------------------------+
|  [StudentHub]   Home | Dashboard | Courses | Assignments ...    |
+-----------------------------------------------------------------+
|                                                                 |
|   +---------------------------------------------------------+   |
|   |                                                         |   |
|   |                      Page Title                         |   |
|   |                                                         |   |
|   |     [ Form Input Field ]                                |   |
|   |     [ Form Input Field ]                                |   |
|   |                                                         |   |
|   |     [ Submit Button ]                                   |   |
|   |                                                         |   |
|   +---------------------------------------------------------+   |
|                                                                 |
+-----------------------------------------------------------------+
```
