# GreenTrans – Smart Transport & Logistics Management System

GreenTrans is a high-fidelity, enterprise-level logistics SaaS platform designed to digitize and optimize transport operations, shipment tracking, fleet management, and delivery workflows. Built with a focus on modern UI/UX and robust backend logic, it caters to customers, drivers, managers, and administrators.

![Banner](https://img.shields.io/badge/GreenTrans-Logistics%20SaaS-10b981?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

### 🌐 [Live Demo](https://greentrans.infinityfree.me)
> **Note:** Experience the platform in action with real-time tracking and 3D illustrations.

---

## 🌟 Key Features

### 👤 Admin Panel (Super Control)
*   **Analytics Dashboard:** Real-time revenue charts and delivery statistics using Chart.js.
*   **Driver Onboarding:** Secure verification workflow for new drivers with document review (License & ID Proof).
*   **User Management:** Centralized control over customers, managers, and drivers.
*   **System Configuration:** Manage platform-wide settings and notifications.

### 🚛 Driver Section (The Mobile-First Experience)
*   **Performance Tracking:** Personal ratings, delivery trends, and completion statistics.
*   **Vehicle Info:** Access to assigned vehicle details and maintenance status.
*   **Support Center:** Integrated ticketing system for route or vehicle issues.
*   **Verification:** Glassmorphic "Pending Approval" screen with document upload capability.

### 📦 Customer Dashboard
*   **Shipment Booking:** Smart booking engine for parcels, documents, and bulk cargo.
*   **Real-time Tracking:** Live status updates from "Order Placed" to "Delivered".
*   **Invoicing:** Automated professional invoice generation.

---

## 🛠️ Technology Stack

*   **Frontend:** HTML5, CSS3 (Vanilla CSS), JavaScript (ES6), Bootstrap 5.
*   **Backend:** PHP (Object-Oriented Architecture).
*   **Database:** MySQL (Relational Schema).
*   **Design:** Premium Glassmorphism & Neomorphism aesthetics.
*   **Charts:** Chart.js for data visualization.

---

## 🚀 Installation Guide

### 1. Prerequisites
*   Install [XAMPP](https://www.apachefriends.org/index.html) or any LAMP/WAMP stack.
*   PHP Version 8.1 or higher.

### 2. Setup
1.  Clone the repository:
    ```bash
    git clone https://github.com/yourusername/GreenTrans-Logistics-SaaS.git
    ```
2.  Move the project folder to `C:\xampp\htdocs\`.
3.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
4.  Create a new database named `greentrans`.
5.  Import the `database.sql` file located in the project root.

### 3. Configuration
1.  Open `config/database.php` and update your database credentials:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'greentrans');
    define('DB_USER', 'root');
    define('DB_PASS', ''); // Update this if you have a MySQL password
    ```
2.  Access the app at `http://localhost/tms`.

---

## 📁 Project Structure

```text
tms/
├── admin/          # Administrative modules
├── api/            # Backend endpoints (AJAX/Uploads)
├── assets/         # CSS, JS, and Images
├── auth/           # Login, Register, and Session logic
├── classes/        # OOP Models (User, Shipment, Vehicle, etc.)
├── config/         # App & Database configuration
├── customer/       # Customer dashboard and features
├── driver/         # Driver-specific modules and performance
├── includes/       # Global components (Navbar, Sidebar, Header)
├── uploads/        # User-uploaded documents and profiles
└── database.sql    # Database backup for migration
```

---

## 🔮 Future Roadmap
*   **AI Integration:** Route optimization for fuel efficiency.
*   **Mobile App:** Flutter-based native apps for Drivers.
*   **IoT:** Real-time sensor tracking for temperature-controlled cargo.

---



---
**Developed with ❤️ by Jasprit Singh Sanu**
