# EStore — E-Commerce Store

A PHP and MySQL e-commerce web application built as a **four-member Web Technologies course project**. EStore brings together customer authentication, an admin-managed product catalogue, a session-based shopping cart and checkout, and post-purchase order and review workflows.

The project uses an **MVC-style structure** with PHP controllers, models, and views, alongside HTML, CSS, JavaScript, AJAX, and MySQL.

> **Project context:** This repository is organized into four student modules (`S1`–`S4`) that use a shared database. Each module was developed as an individually assigned feature.

## Screenshots

### Registration
Customers can register with a name, email, optional phone number, and password.

![EStore registration form](<img width="1347" height="597" alt="Screenshot 2026-11-03 234156" src="https://github.com/user-attachments/assets/4d83a3eb-a4d1-492f-970f-247eeb795f74" />
)

### Product catalogue
The customer-facing catalogue displays products with categories, pricing, ratings, search, category filtering, and add-to-cart controls.

![EStore product catalogue](<img width="1365" height="593" alt="Screenshot 2026-11-03 234243" src="https://github.com/user-attachments/assets/2ad16783-de40-470d-8b37-87ec3bc9da9c" />
)

### Shopping cart
Customers can view selected products, quantities, line totals and the cart total before checkout.

![EStore shopping cart](<img width="1365" height="600" alt="Screenshot 2026-11-03 234817" src="https://github.com/user-attachments/assets/3e724da0-f4da-4d27-a32f-7ff52e8e0218" />
)

### Customer profile
The profile screen provides personal information and saved shipping addresses.

![EStore customer profile](<img width="1365" height="591" alt="Screenshot 2026-11-03 234855" src="https://github.com/user-attachments/assets/92ef9b72-709a-446a-b28c-b300a4b081e1" />
)

### Change password
Customers can update their password by entering their current and new passwords.

![EStore change-password form](<img width="1365" height="593" alt="Screenshot 2026-11-03 234907" src="https://github.com/user-attachments/assets/d2571fe1-d2a2-4508-a596-76fe693c4661" />
)

## Project modules

| Module | Assigned work | Functionality |
| --- | --- | --- |
| **S1** | User Authentication & Profile | Registration, login, remember-me sessions, profile editing, saved shipping addresses, password changes, and admin access control |
| **S2** | Product & Category Management (Admin) | Nested category CRUD, product CRUD, image upload, low-stock indicators, average ratings, and AJAX availability toggle |
| **S3** | Shopping Cart & Checkout (Customer) | Product browsing and filtering, AJAX cart operations, address and payment selection, checkout, and order confirmation |
| **S4** | Order Management & Product Reviews | Customer order history, admin fulfilment/status updates, delivered-product reviews, and rating displays |

### S2 — Product & Category Management

The **Student 2 module** manages the catalogue used by the rest of the store:

- Create, view, edit, and delete categories with **one level of parent/child nesting**.
- Prevent deletion of categories that have child categories or linked products.
- Create, view, edit, and delete products with name, description, category, price, stock quantity, and primary image.
- Validate uploaded product images (JPEG or PNG, up to **3 MB**) and store them under `S2/public/uploads/products/` (or the upload directory configured in the implementation).
- Prevent deletion of products already referenced by order items.
- Highlight products with stock quantity **5 or below**.
- Show average star ratings from the shared reviews table.
- Change product availability using AJAX without reloading the page.

### How the modules connect

1. **S1** authenticates customers and admins and establishes the session/role used by restricted pages.
2. **S2** maintains products, categories, stock information, and image paths.
3. **S3** reads available products and creates orders and order items at checkout.
4. **S4** processes those orders and writes customer reviews that can be displayed against products.

## Technology stack

- **Frontend:** HTML, CSS, JavaScript, AJAX
- **Backend:** PHP
- **Database:** MySQL
- **Local server:** Apache via XAMPP
- **Architecture:** MVC-style organization
- **Database access:** MySQLi / PHP database connection (use prepared statements for user-supplied values)
- **State management:** PHP sessions for authentication and the cart

## Folder organization

```text
E-Commerce-Store/
├── S1/
│   ├── Controller/
│   ├── Model/
│   └── View/
├── S2/
│   ├── Controller/
│   │   ├── CategoryController.php
│   │   └── ProductController.php
│   ├── Model/
│   │   ├── db.php
│   │   ├── CategoryModel.php
│   │   └── ProductModel.php
│   ├── View/
│   └── public/
├── S3/
│   ├── Controller/
│   ├── Model/
│   └── View/
├── S4/
│   ├── Controller/
│   ├── Model/
│   └── View/
├── assets/
│   └── screenshots/
└── README.md
```

> The directory tree summarizes the structure shown during development. Additional files, stylesheets, scripts, and routes may be present in each module.

## Database overview

The modules share six primary tables:

| Table | Purpose |
| --- | --- |
| `users` | Customer/admin accounts, profile information and saved addresses |
| `categories` | Product categories and their parent/child relationships |
| `products` | Product details, category, price, stock, image, and availability |
| `orders` | Customer checkout and order status |
| `order_items` | Products, quantities, and prices associated with orders |
| `reviews` | Customer ratings and written reviews |

The shopping cart is maintained in `$_SESSION['cart']` rather than a dedicated database table.

## Running locally

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) with Apache, PHP, and MySQL
- A modern web browser
- VS Code or another code editor (optional)

### Setup

1. Copy the repository folder to:

   ```text
   C:\xampp\htdocs\E-Commerce-Store
   ```

2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open **http://localhost/phpmyadmin/** and create the database expected by your PHP connection files.
4. Import the project's SQL database export **if one is included**, or create the required shared tables using your team's schema. This README does not assume an SQL dump exists in the repository.
5. Check the database credentials and database name in the relevant connection files, such as `S1/Model/DatabaseConnection.php` and `S2/Model/db.php`.
6. Visit **http://localhost/E-Commerce-Store/** if a root entry point exists. Otherwise, open the actual login or catalogue PHP page under its module's `View/` directory.
7. Register or sign in, then explore the features available for your account role. Admin pages require an account with the admin role.

**Important:** Do not use the VS Code **Go Live** extension to execute PHP; open the application through Apache at `localhost` instead.

## Functional workflow

```text
Register / Login (S1)
       |
       +--> Customer --> Browse products (S3)
       |                    |
       |                 Add to cart
       |                    |
       |                 Checkout (S3)
       |                    |
       |                 My Orders (S4)
       |                    |
       |                 Review delivered items (S4)
       |
       +--> Admin --> Manage categories and products (S2)
                          |
                       Update availability / stock
                          |
                       Manage orders (S4)
```

## Validation and project rules

The course specification calls for server-side form validation, authenticated access to protected pages, prepared database queries, MIME type/size checks for uploads, JSON responses from AJAX endpoints, and safeguards against deleting records used by other modules. Review these rules when testing the integrated application.

## Academic context

Developed as a **Web Technologies** course project by a four-member team. The tasks were individually assigned to Students 1–4, with **Student 2 responsible for Product & Category Management (Admin)**.

