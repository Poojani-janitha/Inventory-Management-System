# 🏭 HealStock Warehouse Management System

**Project Name:** Smart Inventory Management System  
**Organization:** HealStock Pvt Ltd  

---

## 📘 Overview

**HealStock Warehouse Management System** is a web-based inventory management platform designed to optimize warehouse operations for **HealStock Pvt Ltd**, a company that purchases medicines from various vendors and supplies them to other pharmacies.  
The system enhances efficiency in managing **suppliers, users, inventory, products, returns, finances, and reports**, while also integrating **real-time IoT sensor data** and an **AI-powered warehouse chatbot** for smart assistance.


The system ensures secure access through **role-based permissions**:
- 🧑‍💼 **Admin** – Full authorization across all modules  
- 💰 **Financial Staff** – Sales and finance management  
- 📦 **Warehouse Staff** – Product and return management  

---

## ⚙️ Features

✅ Supplier Management  
✅ User Management  
✅ Inventory & Product Management  
✅ Return Management  
✅ Real-Time Sensor Data Display of Humidity and Temparature
✅ Warehouse Chatbot Assistance  
✅ Financial Management & Reports  
✅ Forecasting Module  
✅ Role-Based Access Control (RBAC)  
✅ Real-Time Email Service

---

## 🧩 Installation Guide

Follow these steps to install and run the HealStock system on **XAMPP**:

### 1️⃣ Clone the Repository
Clone this repository into your `htdocs` folder inside your **XAMPP** directory:
```bash
https://github.com/Poojani-janitha/Inventory-Management-System.git
````

### 2️⃣ Create the Database

Open **phpMyAdmin** and create a new database:

```
inventory_system
```

### 3️⃣ Import the SQL File

Import the file located at:

```
DATABASE FILE/updated_sql.sql
```

### 4️⃣ Configure Database Connection

Open:

```
include/config.php
```

Edit the file to match your local XAMPP database credentials:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_system";
```

### 5️⃣ Start the Server

1. Launch **XAMPP Control Panel**
2. Start **Apache** and **MySQL**
3. Open your browser and visit:

```
http://localhost/HealStock-Inventory-Management-System
```

---

## 🔐 Login Credentials

### 👨‍💼 Admin

* **Username:** `prabashi`
* **Password:** `Staff@1`

### 💰 Financial Staff

* **Username:** `sahan`
* **Password:** `Staff@1`

### 📦 Warehouse Staff

* **Username:** `nimhara`
* **Password:** `Staff@1`

---

## 🏗️ Tech Stack

| Category        | Technology                        |
| --------------- | --------------------------------- |
| **Frontend**    | HTML, CSS, Bootstrap              |
| **Backend**     | PHP                               |
| **Database**    | MySQL                             |
| **Server**      | XAMPP (Apache)                    |
| **Other Tools** | JavaScript, AJAX, IoT Integration |

---

## 🧠 About the System

HealStock Warehouse Management System provides a centralized solution for managing all warehouse and financial operations. It allows users to:

* Maintain supplier and product records efficiently
* Monitor real-time inventory updates using IoT sensors
* Manage product returns and stock adjustments
* Generate financial reports and perform forecasting
* Utilize chatbot support for warehouse assistance

This system is designed to **reduce manual effort**, **minimize errors**, and **enhance decision-making** within warehouse operations.

---

## 👥 User Roles

| Role                | Permissions                                                                    |
| ------------------- | ------------------------------------------------------------------------------ |
| **Admin**           | Full control over all modules including user, supplier, and product management |
| **Financial Staff** | Manage sales, payments, invoce generate.                                 |
| **Warehouse Staff** | Add, update, and manage products and returns                                   |

---

## 👨‍💻 Contributors

* **Pooja** –  Admin Module /User managment /chatbot intergration / sensor data display
* **Sahan** – Financial Module /Email generating
* **Nimhara** – Warehouse Module /Email generating
* **Prabashi** – supplier Module / Purchasing /Email generating
* **Kaweesha** – Report Module
* **krishani** – Product Mnagement /Profile Mannagement
* **Vihanga** – Forcasting / Category management


---

## 🙌 Acknowledgment

This project was developed with guidance and inspiration from various **YouTube tutorials** and online learning resources related to PHP, MySQL, and inventory management systems.  
We extend our gratitude to the content creators and developers whose videos helped us understand and implement core functionalities in our system.


## 📜 License

This project is developed for **academic and organizational use** under **HealStock Pvt Ltd**.
© 2025 HealStock Pvt Ltd. All rights reserved.

---

## 🌐 Contact

For support or collaboration:
📧 **[hpjpooja@gmail.com](mailto:hpjpooja@gmail.com)**

---

⭐ **If you like this project, don’t forget to give it a star on GitHub!** ⭐

```




