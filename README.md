# 🌍 Travel Guide - Student Project

**Student ID:** 18-37821-2
**Task 1:** User Authentication, Registration, Profile, Home Page & Wishlist

---

## 🚀 Quick Start Guide

### Step 1: Setup Database (First Time Only)

Open your browser and go to:
```
http://localhost/Final-Lab-Task/setup.php
```

This will create the database and default admin account.

### Step 2: Login as Admin

Go to:
```
http://localhost/Final-Lab-Task/index.php?route=login
```

**Default Admin Credentials:**
- **Email:** `admin@travelguide.com`
- **Password:** `Admin123`

### Step 3: What Can You Do?

#### 👨‍💼 As Admin:
1. **Verify Users** → Go to "Admin Panel" to approve new registrations
2. **View Posts** → See all travel destinations
3. **Manage** → Full control over the system

#### 👤 As General User (after admin approval):
1. **View Posts** → Browse travel destinations
2. **Wishlist** → Save your favorite places (❤️)
3. **Profile** → Update your info and picture

#### 🔍 As Scout (after admin approval):
1. **View Posts** → Browse all content
2. **Profile** → Manage your account

---

## 📁 Project Structure

```
Final-Lab-Task/
├── api/wishlist.php          # AJAX for wishlist
├── config/                   # Database, Session, CSRF
├── controllers/              # All logic (auth, profile, wishlist)
├── css/style.css             # Styles
├── database/schema.sql       # Database structure
├── js/                       # JavaScript validation & AJAX
├── model/                    # Database models (User, Wishlist)
├── public/uploads/           # Profile pictures
├── view/                     # All pages (HTML)
└── index.php                 # Main router
```

---

## 🔑 Default Accounts

| Role   | Email                    | Password   |
|--------|--------------------------|------------|
| Admin  | admin@travelguide.com    | Admin123   |

---

## 📝 How to Test Features

### 1. Registration
```
http://localhost/Final-Lab-Task/index.php?route=register
```
- Fill form with name, email, password (8+ chars, 1 uppercase, 1 number)
- Select role (user/scout/admin)
- Optional: Upload profile picture

### 2. Login
```
http://localhost/Final-Lab-Task/index.php?route=login
```
- Use admin credentials or your registered account
- Check "Remember Me" to stay logged in for 30 days

### 3. Admin Panel
```
http://localhost/Final-Lab-Task/index.php?route=admin
```
- See all pending user verifications
- Click "✓ Verify" to approve users

### 4. Profile
```
http://localhost/Final-Lab-Task/index.php?route=profile
```
- Update name, email
- Change password
- Upload new profile picture

### 5. Wishlist (General Users only)
```
http://localhost/Final-Lab-Task/index.php?route=wishlist
```
- Add posts to wishlist from home page
- View all saved posts
- Remove from wishlist

---

## ✅ Features Implemented

| Feature | Status |
|---------|--------|
| User Registration | ✅ Multi-role (Admin/Scout/User) |
| Login System | ✅ With "Remember Me" (30 days) |
| Profile Management | ✅ Update info, change password, upload picture |
| Admin Verification | ✅ Admin approves new users |
| Home Page | ✅ Dynamic based on auth/role |
| Wishlist (CRUD) | ✅ Add/View/Remove via AJAX |
| Security | ✅ SQL injection, XSS, CSRF protection |
| Validation | ✅ Client + Server side |
| MVC Pattern | ✅ Proper separation |

---

## 🛡️ Security Features

1. **SQL Injection Prevention** → PDO prepared statements
2. **XSS Protection** → All output escaped
3. **CSRF Tokens** → Form validation
4. **Password Hashing** → `password_hash()` / `password_verify()`
5. **Session Management** → Secure session handling
6. **File Upload Validation** → MIME type + size check

---

## 📱 Pages Overview

| Page | URL | Who Can Access |
|------|-----|----------------|
| Home | `?route=home` | Everyone |
| Register | `?route=register` | Everyone |
| Login | `?route=login` | Everyone |
| Profile | `?route=profile` | Logged in users |
| Wishlist | `?route=wishlist` | Verified General Users |
| Admin Panel | `?route=admin` | Verified Admins |

---

## 🐛 Troubleshooting

### CSS not loading?
- Make sure you're accessing via `http://localhost/Final-Lab-Task/`
- Clear browser cache

### 302 Error on register?
- Make sure setup.php was run first
- Check browser console for errors

### Database errors?
- Run setup.php again
- Check XAMPP MySQL is running

---

## 📧 Contact

For issues with this project, please contact your instructor.

---

**Made with ❤️ for educational purposes**
