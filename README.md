#  Library Management System API (Laravel)

A RESTful **Library Management System API** built with **Laravel** and **Laravel Sanctum**, featuring **role-based access control** for **Guests, Members, and Admins**.

This project is suitable for academic and resume purposes and demonstrates:
- REST API design
- Token-based authentication
- Role-based authorization
- Relational database modeling
- Artisan command usage

---

##  Tech Stack

- Laravel
- Laravel Sanctum
- SQLite
- Eloquent ORM
- RESTful APIs

---

##  User Roles

### 🔹 Guest
- View all books
- View book details
- Search books
- Register
- Login

### 🔹 Member
- All guest permissions
- Borrow books
- Return books
- View own loans
- View own fines
- View own profile
- Logout

### 🔹 Admin
- All member permissions
- Manage books (CRUD)
- View all members
- View active loans
- Issue fines (individually or for all)
- Mark fines as paid

---

##  Authentication

This API uses **Laravel Sanctum** for authentication.

- Tokens are generated on login
- Tokens are stored in the `personal_access_tokens` table
- All protected requests require the header:

 **Authorization: Bearer YOUR_API_TOKEN**

---

##  API Endpoints

###  Public / Guest Routes

| Method | Endpoint | Description |
|------|---------|------------|
| GET | `/books` | List all books |
| GET | `/book/{book}` | View book details |
| GET | `/books/search` | Search books |
| POST | `/register` | Register as a member |
| POST | `/login` | Login and receive token |

---

###  Member Routes (Authenticated)

Middleware: `auth:sanctum`

| Method | Endpoint | Description |
|------|---------|------------|
| GET | `/user/profile` | View user profile |
| POST | `/logout` | Logout user |
| POST | `/loans/{book}/borrow` | Borrow a book |
| POST | `/loans/{loan}/return` | Return a book |
| GET | `/user/loans` | View user loans |
| GET | `/user/fines` | View user fines |

---

###  Admin Routes

Prefix: `/admin`  
Middleware: `auth:sanctum`, `role:admin`

| Method | Endpoint | Description |
|------|---------|------------|
| POST | `/admin/books/add` | Add new book |
| DELETE | `/admin/books/{book}` | Delete book |
| PUT | `/admin/books/edit/{book:isbn}` | Update book by ISBN |
| GET | `/admin/members` | View all members |
| GET | `/admin/members/{member}` | View member details |
| GET | `/admin/loans` | View active loans |
| POST | `/admin/loans/{loan}/fine` | Issue fine |
| PUT | `/admin/fines/{fine}/paid` | Mark fine as paid |

---

##  Artisan Commands

###  Create Admin User

Creates an admin account securely via the terminal.

```bash
php artisan create:user
```
### Issue fine 
Issues fine to loan with due_date before the current time.

```bash
php artisan issue:fines-for-all
```