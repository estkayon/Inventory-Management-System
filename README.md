# 📦 Inventory Management System (Barcode-Based)

A complete **Inventory Management System** built using **PHP, MySQL, and Bootstrap**, designed to track products using **unique barcodes** throughout their lifecycle — from tracking to delivery.

---

## 🚀 Features

### 👤 Multi-User Role System
- Admin
- Component Tracker
- Receive User
- Delivery User
- Report Viewer

Each role has controlled access and specific responsibilities.

---

### 🏷️ Barcode-Based Tracking
- Generate unique 11-character barcodes
- Assign barcodes to products and components
- Track product lifecycle using barcode

---

### 🔄 Product Lifecycle Flow

1. **Component Tracker**
   - Generates barcode
   - Assigns product + components
   - Sends to receive point

2. **Receive User**
   - Receives product (scan barcode)
   - Stock updated (IN)
   - Sends to delivery point

3. **Delivery User**
   - Delivers product (scan barcode)
   - Stock updated (OUT)

4. **Report Viewer**
   - Views reports & history

---

### 📊 Reports
- Stock Report
- Summary Report (with date filter)
- Barcode Wise History (full lifecycle tracking)

---

### 📦 Stock Management
- Automatic stock update
- Tracks:
  - stock_in
  - stock_out
- Real-time inventory visibility

---

### 🧾 Activity Logs
- Every action is recorded:
  - tracked
  - received
  - sent to delivery
  - delivered

---

## 🛠️ Technologies Used

| Category        | Technology |
|----------------|-----------|
| Backend        | PHP       |
| Database       | MySQL     |
| Frontend       | HTML      |
| Styling        | Bootstrap |
| Server         | Apache (XAMPP) |
| Security       | password_hash(), password_verify() |

---

## 🗄️ Database Design

Main Tables:
- users
- products
- components
- product_barcodes
- product_components
- stock
- receive_points
- delivery_points
- activity_logs



