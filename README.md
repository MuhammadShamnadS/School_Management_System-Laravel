# 📚 School Management System (Laravel 12 + JWT)

A role-based school management system built with **Laravel 12** using **JWT Authentication**.  
This system supports **Admin**, **Teacher**, and **Student** roles with different access levels.

---

## 🚀 Features

### 🔹 Admin
- Login with JWT
- Register Teachers and Students (creates linked user records)
- CRUD operations on Teachers and Students
- View all Teachers and Students

### 🔹 Teacher
- Login with JWT
- View only their own profile
- View students assigned to them

### 🔹 Student
- Login with JWT
- View only their own profile

---

## 🛠️ Tech Stack
- **Backend:** Laravel 12
- **Authentication:** JWT (`tymon/jwt-auth`)
- **Database:** MySQL
- **Server:** XAMPP (PHP 8.x)

---

## 📂 Project Structure

    app/
    └── Http/
    ├── Controllers/
    │ └── AuthController.php # Handles login & registration
    │ └── TeacherController.php
    │ └── StudentController.php
    └── Middleware/
    └── RoleMiddleware.php # Role-based access control
    database/
    └── migrations/ # Database schema
    routes/
    └── api.php # API Routes


## Install dependencies
    composer install