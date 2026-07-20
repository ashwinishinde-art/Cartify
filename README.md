<img width="195" height="670" alt="Screenshot 2026-07-16 at 11 08 42 AM" src="https://github.com/user-attachments/assets/2eb27551-f2bf-46b1-b37a-8908a6ec8253" /># Cartify
# Cartify

Cartify is a complete e-commerce platform designed to provide a secure, responsive, and user-friendly online shopping experience. The system accommodates both buyers looking for standard retail purchases and vendors managing their own storefront operations through a tailored merchant portal.

## 🚀 Features

* **User & Session Management:** Secure user registration, authentication, and unique dashboard interfaces depending on the account role (Customer vs. Merchant)[cite: 2, 3].
* **Dynamic Shopping Cart:** Real-time item count updates and persistent quantity management via server sessions[cite: 2, 3].
* **Search & Discovery:** A robust navigation bar featuring live text filtering for brands and specific items[cite: 2, 3].
* **Merchant Ecosystem:** A dedicated "Become a Seller" onboarding workflow that fast-tracks merchants into a profile dashboard[cite: 2, 3].
* **Showcase Sections:** Clean visual layouts for popular product categories, automated discount percentage calculation tags, "Deals of the Day", and an interactive customer review carousel[cite: 2].
* **Order Fulfillment:** Integrated client dashboard tools for live order status monitoring and tracking[cite: 2, 3].

## 🛠️ Technology Stack

* **Frontend:** HTML , CSS, JavaScript .
* **Backend:** PHP (Session tracking, page routing, and dynamic template rendering)[cite: 2, 3]
* **Database:** MySQL
* **Server Environment:** Apache (XAMPP local development lifecycle framework)

## image 

![Uploading Screenshot 2026-07-16 at 11.08.42 AM.png…]()


## 📂 Project Structure

```text
├── index.php                 # Core storefront landing page & customer reviews hub[cite: 2]
├── logout.php                # Explicit session destruction and route protection handler[cite: 2]
├── style.css                 # Base layout, structural components, and grid stylings[cite: 2]
├── header.css                # Global navigation bar styling and dynamic counter overlays[cite: 2]
├── Assets/                   # Static directory for UI illustrations, system icons, and logos[cite: 2]
├── Merchant/                 # Seller pipeline interfaces[cite: 2]
│   └── dashboard.php         # Vendor control panel and profile inventory overview[cite: 2]
└── Products/                 # E-commerce core pages[cite: 2]
    ├── products.php          # Category filtration results and textual search indexing[cite: 2]
    ├── cart.php              # Multi-item checkout pipeline[cite: 2]
    └── track-orders.php      # Live updates for customer order status[cite: 2]
