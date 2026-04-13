# 🛒 PHP E-Commerce Web App

> A full-featured PHP-based e-commerce web application with separate Buyer and Seller roles. Includes product management, shopping cart, checkout system, order tracking, and secure user authentication with input validation.

---

## 🛠️ Technologies Used

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- WAMP / XAMPP (Local Server)

---

## 📂 Project Structure

```
php-ecommerce/
├── lib/                        # External libraries
├── uploads/                    # Product image uploads
├── db.php                      # Database connection
├── page.php                    # Main page handler
├── login.php                   # Login logic
├── login_page.php              # Login UI
├── logout.php                  # Logout handler
├── data_ins.php                # Data insertion handler
├── validation.php              # Form validation
├── buyer_dashboard.php         # Buyer main dashboard
├── cart.php                    # Shopping cart
├── checkout.php                # Checkout page
├── set_checkout_session.php    # Session handler for checkout
├── place_order.php             # Order placement
├── my_orders.php               # Buyer order history
├── seller_dashboard.php        # Seller main dashboard
├── seller_products.php         # Seller product listing
├── add_product.php             # Add new product
├── insert_product.php          # Product insert handler
├── edit_product.php            # Edit existing product
├── update_product.php          # Product update handler
├── delete_product.php          # Delete product
├── update_order_status.php     # Order status management
├── view_orders.php             # Seller order view
├── .gitignore                  # Ignored files
└── README.md                   # Project documentation
```

---

## ⚙️ Setup & Installation

### 1. Clone the Repository
```bash
git clone https://github.com/muhammadabdullahsaif49/php-ecommerce.git
```

### 2. WAMP/XAMPP Setup
- Project folder ko `www` ya `htdocs` mein rakho
- WAMP/XAMPP start karo

### 3. Database Setup
- phpMyAdmin open karo
- Naya database banao (e.g. `ecommerce_db`)
- SQL file import karo (agar available ho)

### 4. DB Connection Configure karo
`db.php` mein apni settings daalo:
```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "ecommerce_db";
```

### 5. Run
Browser mein open karo:
```
http://localhost/php-ecommerce/page.php
```

---

## ✨ Features

- 🔐 **User Authentication** — Secure login, logout aur session management
- 👤 **Multi-Role System** — Alag Buyer aur Seller dashboards
- 📦 **Product Management** — Seller products add, edit, delete kar sakta hai
- 🖼️ **Image Upload** — Product images upload support
- 🛒 **Shopping Cart** — Buyer products cart mein add kar sakta hai
- 💳 **Checkout System** — Session-based secure checkout flow
- 📋 **Order Placement** — Buyer orders place kar sakta hai
- 📜 **Order History** — Buyer apne purane orders dekh sakta hai
- 🔄 **Order Status Update** — Seller order status update kar sakta hai
- ✅ **Form Validation** — Input validation se secure data handling

---

## 👤 Author

**Muhammad**  
GitHub: [@muhammadabdullahsaif49](https://github.com/muhammadabdullahsaif49)

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).
