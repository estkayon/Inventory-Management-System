# 📦 Inventory Management System — Barcode-Based Product Tracking

A complete **Barcode-Based Inventory Management System** developed using **PHP, MySQL, JavaScript, and Bootstrap**.

The system is designed to manage and track products throughout their complete operational lifecycle — from **barcode generation and component assignment to receiving, stock management, delivery, reporting, and activity tracking**.

It provides a **multi-role workflow**, allowing different users to perform specific responsibilities while maintaining centralized product and inventory information.

---

## 🚀 Key Features

### 👥 Multi-User Role Management

The system provides role-based access for different operational users:

- **Admin**
- **Component Tracker**
- **Receive User**
- **Delivery User**
- **Report Viewer**

Each role has specific permissions and responsibilities within the inventory workflow.

---

## 🏷️ Barcode-Based Product Tracking

The system uses unique barcodes to identify and track products throughout their lifecycle.

### Barcode Features

- Generate unique **11-character barcodes**
- Assign barcodes to individual products
- Associate components with products
- Search and track products using barcode
- Maintain barcode-wise transaction history
- Monitor the complete product lifecycle

Each barcode acts as the primary tracking identifier for a product.

---

## 🔄 Product Lifecycle Workflow

The complete inventory workflow follows four major operational stages.

### 1. Component Tracker

The Component Tracker prepares products before they enter the inventory workflow.

Responsibilities include:

- Generate a unique barcode
- Select or assign a product
- Assign required components
- Record product tracking information
- Send the product to the receive point

---

### 2. Receive User

The Receive User handles incoming products.

Responsibilities include:

- Scan or enter the product barcode
- Verify product information
- Receive the product
- Update inventory as **Stock IN**
- Record the receive activity
- Send the product to the delivery point

---

### 3. Delivery User

The Delivery User handles outgoing products.

Responsibilities include:

- Scan or enter the product barcode
- Verify product information
- Process product delivery
- Update inventory as **Stock OUT**
- Record delivery information
- Complete the product lifecycle

---

### 4. Report Viewer

The Report Viewer can monitor inventory information and product history.

Available reports include:

- Current stock information
- Product movement summary
- Barcode-wise lifecycle history
- Date-based inventory reports

---

## 📊 Reporting System

The application provides multiple reports for monitoring inventory activities.

### Stock Report

Displays the current stock information based on product transactions.

### Summary Report

Provides an overview of inventory activities.

Features include:

- Date-based filtering
- Stock IN summary
- Stock OUT summary
- Inventory movement overview

### Barcode-Wise History

Allows users to search a barcode and view its complete lifecycle.

Example lifecycle:

```text
Barcode Generated
      ↓
Product & Components Assigned
      ↓
Sent to Receive Point
      ↓
Product Received
      ↓
Stock IN
      ↓
Sent to Delivery Point
      ↓
Product Delivered
      ↓
Stock OUT
```

---

## 📦 Stock Management

The system automatically manages inventory quantities based on product movement.

### Stock Operations

- **Stock IN** when a product is received
- **Stock OUT** when a product is delivered
- Automatic inventory updates
- Current stock visibility
- Product movement tracking

This helps reduce manual stock calculation and maintains consistent inventory records.

---

## 🧾 Activity Logging

Important system actions are recorded to maintain traceability.

Tracked activities include:

- Product tracked
- Barcode generated
- Product received
- Product sent to delivery
- Product delivered
- Stock updated

Activity logs provide a historical record of product movement and system operations.

---

## 🔐 Authentication & Security

The application includes user authentication and role-based access control.

Security features include:

- Secure login system
- Role-based authorization
- Restricted page access
- Password hashing
- Password verification

PHP security functions used:

```php
password_hash()
password_verify()
```

---

## 🛠️ Technologies Used

| Category | Technology |
|---|---|
| Backend | PHP |
| Database | MySQL |
| Frontend | HTML, JavaScript |
| Styling | Bootstrap |
| Web Server | Apache |
| Local Development | XAMPP |
| Database Management | phpMyAdmin |
| Authentication | PHP Session |
| Password Security | `password_hash()`, `password_verify()` |

---

## 🗄️ Database Structure

The system uses a relational MySQL database.

### Main Tables

```text
users
products
components
product_barcodes
product_components
stock
receive_points
delivery_points
activity_logs
```

### Table Responsibilities

| Table | Purpose |
|---|---|
| `users` | Stores system users and roles |
| `products` | Stores product information |
| `components` | Stores component information |
| `product_barcodes` | Stores generated product barcodes |
| `product_components` | Maintains product-component relationships |
| `stock` | Stores inventory and stock movement information |
| `receive_points` | Stores receiving-related information |
| `delivery_points` | Stores delivery-related information |
| `activity_logs` | Records system and product activities |

---

## 🏗️ System Workflow

```text
                    ┌─────────────────────┐
                    │  Component Tracker  │
                    └──────────┬──────────┘
                               │
                               ▼
                    Generate Product Barcode
                               │
                               ▼
                  Assign Product + Components
                               │
                               ▼
                       Send to Receive
                               │
                               ▼
                    ┌─────────────────────┐
                    │    Receive User     │
                    └──────────┬──────────┘
                               │
                               ▼
                        Scan Barcode
                               │
                               ▼
                         Receive Product
                               │
                               ▼
                           Stock IN
                               │
                               ▼
                      Send to Delivery
                               │
                               ▼
                    ┌─────────────────────┐
                    │    Delivery User    │
                    └──────────┬──────────┘
                               │
                               ▼
                        Scan Barcode
                               │
                               ▼
                        Deliver Product
                               │
                               ▼
                          Stock OUT
                               │
                               ▼
                    Product Lifecycle Complete
```

---

## ⚙️ Installation & Setup

### Prerequisites

Make sure the following are installed:

- XAMPP
- Apache
- MySQL
- PHP
- phpMyAdmin
- Web Browser

---

### 1. Clone the Repository

```bash
git clone https://github.com/estkayon/Inventory-Management-System.git
```

---

### 2. Move the Project to XAMPP

Move the project folder into:

```text
C:\xampp\htdocs\
```

Example:

```text
C:\xampp\htdocs\inventory-management-system
```

---

### 3. Start XAMPP

Open the **XAMPP Control Panel** and start:

```text
Apache
MySQL
```

---

### 4. Create the Database

Open:

```text
http://localhost/phpmyadmin
```

Create a database for the project.

Then import the provided `.sql` database file if one is included in the repository.

---

### 5. Configure Database Connection

Update the database configuration according to your local environment.

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "your_database_name";
```

---

### 6. Run the Application

Open your browser and visit:

```text
http://localhost/inventory-management-system/
```

---



## 🎯 Project Objectives

The main objectives of this project are to:

- Digitize inventory tracking
- Reduce manual inventory operations
- Track products using unique barcodes
- Maintain product lifecycle visibility
- Automate stock IN and stock OUT operations
- Provide centralized reporting
- Maintain historical activity records
- Apply role-based workflow management

---

## 💡 What This Project Demonstrates

This project demonstrates practical implementation of:

- Full-stack web development
- PHP backend development
- MySQL relational database design
- CRUD operations
- Role-based access control
- Authentication and authorization
- Barcode-based product tracking
- Inventory workflow automation
- Stock management
- Reporting systems
- Activity logging
- Multi-user business workflows

---

## 🔮 Future Improvements

Possible improvements include:

- QR code support
- Barcode scanner integration
- REST API development
- Export reports to PDF/Excel
- Email notifications
- Advanced dashboard analytics
- Product search and filtering
- Low-stock alerts
- Responsive mobile interface
- Audit log management
- Cloud deployment

---

## 👨‍💻 Author

**Md. Estiak Rahman Ayon**

Computer Science Graduate  
American International University-Bangladesh

Areas of Interest:

- Software Engineering
- Full-Stack Development
- Artificial Intelligence
- Machine Learning
- MLOps

---

## 📄 License

This project was developed for learning, portfolio, and practical software development purposes.