<div align="center">
  <img src="../assets/logo.png" alt="Cartify Logo" width="300">
  
  > Everything You Need, All in One Cart....
</div>


## 🚀 Features

### 🛍️ User Experience & Frontend
* **Dynamic Search & Filtering:** Live expandable header search bar with instant query parameter execution[cite: 2, 3, 5].
* **Product Discovery:** Featured deals of the day, top categories (Fashion, Electronics, Home, etc.), and top-rated popular products[cite: 3].
* **Interactive Shopping Cart:** Real-time session-based cart badge with dynamic item count updates[cite: 3].
* **Infinite Customer Review Carousel:** Custom JavaScript carousel featuring smooth continuous loop mechanics for customer feedback[cite: 5].
* **Responsive Layout:** Fully optimized for mobile, tablet, and desktop viewports using CSS Flexbox and Grid[cite: 6, 7].

### 🔐 Auth & Account Features
* **User Authentication:** Secure user registration, login, and session-managed logouts[cite: 3, 4, 6].
* **Role-Based Access Control (RBAC):** Distinct roles and routing for Customers, Sellers/Merchants, and Administrators[cite: 3, 6].
* **Merchant Portal:** Quick-access merchant onboarding form and dedicated profile dashboard[cite: 3].

### ⚙️ Core Operations
* **Order Tracking:** Track purchase statuses directly from the dynamic user menu dropdown[cite: 3].
* **PDO Database Connector:** Robust MySQL database connections using PHP Data Objects (PDO) with exception handling and standard error reporting.

---

## 🛠️ Technology Stack

* **Frontend:** HTML5, CSS3 (Google Fonts: *Inter*, *Frank Ruhl Libre*, *Fredoka*, *Just Another Hand*, *Monda*), Vanilla JavaScript ES6+[cite: 2, 3, 6, 7]
* **Backend:** Native PHP (Sessions, PDO)[cite: 1, 3, 4, 6]
* **Database:** MySQL
* **Local Development Environment:** XAMPP / MAMP / WAMP (Apache Server)[cite: 1, 6]

---

## Image 

<img width="195" height="670" alt="Screenshot 2026-07-16 at 11 08 42 AM" src="https://github.com/user-attachments/assets/2eb27551-f2bf-46b1-b37a-8908a6ec8253" />#


## 📁 Project Structure

```text
Cartify/
├── About/
│   └── about.html            # About Us page
├── Assets/                   # Visual assets and product imagery
│   └── logo.png              # Brand Logo
├── Contact/
│   └── contact.html          # Customer support contact page
├── Merchant/                 # Merchant portal and onboarding workflow
│   ├── dashboard.php
│   └── index.php
├── Privacy and policy/       # Legal documents
│   ├── privacy-policy.html
│   ├── return-policy.html
│   └── terms-conditions.html
├── Products/                 # Product catalog & shopping workflow
│   ├── cart.php              # Cart processing & UI
│   ├── products.php          # Product catalog search results
│   └── track-orders.php      # Customer order tracking
├── db.php                    # MySQL Database connection via PDO
├── header.css                # Navigation & header stylesheet
├── index.php                 # Main Homepage
├── logout.php                # Session destruction & redirect handler
├── script.js                 # Carousel logic & interactive search bar
└── style.css                 # Primary site layout & section design
