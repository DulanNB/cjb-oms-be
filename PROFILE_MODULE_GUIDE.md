# Admin Profile Module - Complete Structure Guide

## 📁 Directory Structure

```
src/
└── Admin/
    ├── AuthController.php                    (Original admin auth)
    └── Profile/
        ├── Controllers/
        │   └── AuthController.php           ✅ Profile authentication
        ├── Requests/
        │   ├── LoginRequest.php             ✅ Login validation
        │   └── RegisterRequest.php          ✅ Register validation
        └── api.php                          ✅ Profile routes handler
```

## 🔗 Route Structure

All Profile routes are under: `/api/admin/profile`

### Public Routes
- `POST /api/admin/profile/register` - Register new admin profile
- `POST /api/admin/profile/login` - Login to admin profile

### Protected Routes (Require Token)
- `GET /api/admin/profile/profile` - Get profile details
- `PUT /api/admin/profile/profile` - Update profile
- `DELETE /api/admin/profile/profile` - Delete profile
- `POST /api/admin/profile/logout` - Logout

---

## 🧪 Postman Testing Guide

### 1. REGISTER ADMIN PROFILE

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "name": "Admin Profile User",
    "email": "adminprofile@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "Admin registration successful",
    "user": {
        "name": "Admin Profile User",
        "email": "adminprofile@example.com",
        "updated_at": "2025-11-05T16:30:00.000000Z",
        "created_at": "2025-11-05T16:30:00.000000Z",
        "id": 1
    },
    "access_token": "3|abcd1234...",
    "token_type": "Bearer"
}
```

---

### 2. LOGIN ADMIN PROFILE

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Body (JSON):**
```json
{
    "email": "adminprofile@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "message": "Admin login successful",
    "user": {
        "id": 1,
        "name": "Admin Profile User",
        "email": "adminprofile@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:30:00.000000Z",
        "updated_at": "2025-11-05T16:30:00.000000Z"
    },
    "access_token": "4|xyz9876...",
    "token_type": "Bearer"
}
```

**⚠️ COPY THE TOKEN!**

---

### 3. GET PROFILE

**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
    "profile": {
        "id": 1,
        "name": "Admin Profile User",
        "email": "adminprofile@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:30:00.000000Z",
        "updated_at": "2025-11-05T16:30:00.000000Z"
    }
}
```

---

### 4. UPDATE PROFILE

**Method:** `PUT`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Body (JSON) - Update Name:**
```json
{
    "name": "Updated Admin Name"
}
```

**Body (JSON) - Update Email:**
```json
{
    "email": "newemail@example.com"
}
```

**Body (JSON) - Update Password:**
```json
{
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Body (JSON) - Update All:**
```json
{
    "name": "Completely New Name",
    "email": "completelynew@example.com",
    "password": "newpass456",
    "password_confirmation": "newpass456"
}
```

**Response (200):**
```json
{
    "message": "Profile updated successfully",
    "user": {
        "id": 1,
        "name": "Updated Admin Name",
        "email": "newemail@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:30:00.000000Z",
        "updated_at": "2025-11-05T16:35:00.000000Z"
    }
}
```

---

### 5. DELETE PROFILE

**Method:** `DELETE`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
    "message": "Profile deleted successfully"
}
```

**Note:** This will delete the account and revoke all tokens.

---

### 6. LOGOUT

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/logout`

**Headers:**
```
Accept: application/json
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
    "message": "Admin logged out successfully"
}
```

---

## 📊 Complete API Routes Overview

### Regular User Routes
| Endpoint | Method | Auth | Module |
|----------|--------|------|--------|
| /api/register | POST | No | User |
| /api/login | POST | No | User |
| /api/user | GET | Yes | User |
| /api/logout | POST | Yes | User |

### Admin Routes (Old)
| Endpoint | Method | Auth | Module |
|----------|--------|------|--------|
| /api/admin/register | POST | No | Admin |
| /api/admin/login | POST | No | Admin |
| /api/admin/user | GET | Yes | Admin |
| /api/admin/logout | POST | Yes | Admin |
| /api/admin/users | GET | Yes | Admin |
| /api/admin/users/{id} | PUT | Yes | Admin |
| /api/admin/users/{id} | DELETE | Yes | Admin |

### Admin Profile Routes (New)
| Endpoint | Method | Auth | Module |
|----------|--------|------|--------|
| /api/admin/profile/register | POST | No | Profile |
| /api/admin/profile/login | POST | No | Profile |
| /api/admin/profile/profile | GET | Yes | Profile |
| /api/admin/profile/profile | PUT | Yes | Profile |
| /api/admin/profile/profile | DELETE | Yes | Profile |
| /api/admin/profile/logout | POST | Yes | Profile |

---

## 🔍 Features

### Request Validation
✅ **LoginRequest** - Custom validation for login
- Email required and must be valid email format
- Password required and minimum 8 characters

✅ **RegisterRequest** - Custom validation for registration
- Name required, max 255 characters
- Email required, valid format, unique in database
- Password required, min 8 characters, must be confirmed

### Controller Methods
✅ `register()` - Create new admin profile
✅ `login()` - Authenticate admin profile
✅ `getProfile()` - Get authenticated user's profile
✅ `updateProfile()` - Update profile (name, email, password)
✅ `deleteProfile()` - Delete account and all tokens
✅ `logout()` - Logout and revoke current token

### Security Features
✅ Token-based authentication (Sanctum)
✅ Password hashing with bcrypt
✅ Form request validation
✅ Custom validation messages
✅ Token revocation on logout/delete

---

## 🎯 Testing Workflow

### Complete Test Flow:
1. **Register** → Get token
2. **Get Profile** → Verify user data
3. **Update Profile** → Change name/email/password
4. **Get Profile** → Verify updates
5. **Logout** → Token revoked
6. **Login** → Get new token
7. **Delete Profile** → Account removed

### Test Invalid Cases:
- Register with existing email (should fail)
- Login with wrong password (should fail)
- Access profile without token (should fail - 401)
- Update with invalid data (should fail - 422)

---

## 📝 File Details

### AuthController.php
**Location:** `src/Admin/Profile/Controllers/AuthController.php`  
**Namespace:** `Src\Admin\Profile\Controllers`  
**Methods:** 6 (register, login, logout, getProfile, updateProfile, deleteProfile)

### LoginRequest.php
**Location:** `src/Admin/Profile/Requests/LoginRequest.php`  
**Namespace:** `Src\Admin\Profile\Requests`  
**Validates:** email, password

### RegisterRequest.php
**Location:** `src/Admin/Profile/Requests/RegisterRequest.php`  
**Namespace:** `Src\Admin\Profile\Requests`  
**Validates:** name, email, password, password_confirmation

### api.php
**Location:** `src/Admin/Profile/api.php`  
**Loaded by:** `routes/api.php` (via require statement)  
**Prefix:** `/api/admin/profile`

---

## 💡 How Routes Are Loaded

**In `routes/api.php`:**
```php
Route::prefix('admin')->group(function () {
    // ... other admin routes ...
    
    // Admin Profile Routes - Load from Profile/api.php
    Route::prefix('profile')->group(function () {
        require __DIR__ . '/../src/Admin/Profile/api.php';
    });
});
```

This creates the hierarchy:
- `api/` (base)
  - `admin/` (admin prefix)
    - `profile/` (profile prefix)
      - Routes from `Profile/api.php`

---

## 🚀 Quick Start Commands

```bash
# Start Laravel server
php artisan serve

# View all routes
php artisan route:list

# View only profile routes
php artisan route:list --path=admin/profile

# Clear cache if needed
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## ✅ Structure Verification

Run this command to verify all routes are loaded:
```bash
php artisan route:list --path=admin/profile
```

Expected output: 6 routes under `api/admin/profile`

---

**Ready to test!** 🎉

Your structure matches the image you provided:
```
src/Admin/Profile/
├── Controllers/AuthController.php
├── Requests/LoginRequest.php
├── Requests/RegisterRequest.php
└── api.php
```
