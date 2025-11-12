# Admin Authentication API - Postman Testing Guide

## 📁 File Structure

```
src/
└── Admin/
    └── AuthController.php  ✅ Created
```

## 🔐 Admin API Endpoints

Base URL: `http://127.0.0.1:8000/api/admin`

---

## 📋 Test 1: ADMIN REGISTER

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/admin/register`

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON - raw)
```json
{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "admin123456",
    "password_confirmation": "admin123456"
}
```

### Expected Response (201 Created)
```json
{
    "message": "Admin registration successful",
    "user": {
        "name": "Admin User",
        "email": "admin@example.com",
        "updated_at": "2025-11-05T16:00:00.000000Z",
        "created_at": "2025-11-05T16:00:00.000000Z",
        "id": 1
    },
    "access_token": "1|abcd1234...",
    "token_type": "Bearer"
}
```

---

## 📋 Test 2: ADMIN LOGIN

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/admin/login`

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON - raw)
```json
{
    "email": "admin@example.com",
    "password": "admin123456"
}
```

### Expected Response (200 OK)
```json
{
    "message": "Admin login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:00:00.000000Z",
        "updated_at": "2025-11-05T16:00:00.000000Z"
    },
    "access_token": "2|xyz9876...",
    "token_type": "Bearer"
}
```

**⚠️ Copy the `access_token` for protected admin routes!**

---

## 📋 Test 3: GET ADMIN USER (Protected)

### Request Setup
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/admin/user`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ADMIN_TOKEN
```

### Expected Response (200 OK)
```json
{
    "admin": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:00:00.000000Z",
        "updated_at": "2025-11-05T16:00:00.000000Z"
    }
}
```

---

## 📋 Test 4: GET ALL USERS (Admin Protected)

### Request Setup
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/admin/users`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ADMIN_TOKEN
```

### Expected Response (200 OK)
```json
{
    "users": [
        {
            "id": 1,
            "name": "Admin User",
            "email": "admin@example.com",
            "email_verified_at": null,
            "created_at": "2025-11-05T16:00:00.000000Z",
            "updated_at": "2025-11-05T16:00:00.000000Z"
        },
        {
            "id": 2,
            "name": "John Doe",
            "email": "john@example.com",
            "email_verified_at": null,
            "created_at": "2025-11-05T16:05:00.000000Z",
            "updated_at": "2025-11-05T16:05:00.000000Z"
        }
    ],
    "total": 2
}
```

---

## 📋 Test 5: UPDATE USER (Admin Protected)

### Request Setup
- **Method:** `PUT`
- **URL:** `http://127.0.0.1:8000/api/admin/users/2`

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer YOUR_ADMIN_TOKEN
```

### Body (JSON - raw)
```json
{
    "name": "John Updated",
    "email": "john.updated@example.com"
}
```

### Expected Response (200 OK)
```json
{
    "message": "User updated successfully",
    "user": {
        "id": 2,
        "name": "John Updated",
        "email": "john.updated@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T16:05:00.000000Z",
        "updated_at": "2025-11-05T16:10:00.000000Z"
    }
}
```

---

## 📋 Test 6: DELETE USER (Admin Protected)

### Request Setup
- **Method:** `DELETE`
- **URL:** `http://127.0.0.1:8000/api/admin/users/2`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ADMIN_TOKEN
```

### Expected Response (200 OK)
```json
{
    "message": "User deleted successfully"
}
```

---

## 📋 Test 7: ADMIN LOGOUT

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/admin/logout`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ADMIN_TOKEN
```

### Expected Response (200 OK)
```json
{
    "message": "Admin logged out successfully"
}
```

---

## 📊 Complete API Routes Summary

### User Routes (Regular Users)
| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| /api/register | POST | No | User registration |
| /api/login | POST | No | User login |
| /api/user | GET | Yes | Get user profile |
| /api/logout | POST | Yes | User logout |

### Admin Routes
| Endpoint | Method | Auth | Description |
|----------|--------|------|-------------|
| /api/admin/register | POST | No | Admin registration |
| /api/admin/login | POST | No | Admin login |
| /api/admin/user | GET | Yes | Get admin profile |
| /api/admin/logout | POST | Yes | Admin logout |
| /api/admin/users | GET | Yes | Get all users |
| /api/admin/users/{id} | PUT | Yes | Update user |
| /api/admin/users/{id} | DELETE | Yes | Delete user |

---

## 🔄 Complete Testing Flow

### Step 1: Create Admin
```
POST /api/admin/register
→ Copy admin token
```

### Step 2: Create Regular User
```
POST /api/register
→ This creates a regular user
```

### Step 3: Admin Gets All Users
```
GET /api/admin/users
(with admin token)
→ Should see both admin and regular user
```

### Step 4: Admin Updates User
```
PUT /api/admin/users/2
(with admin token)
→ Update the regular user's info
```

### Step 5: Admin Deletes User
```
DELETE /api/admin/users/2
(with admin token)
→ Delete the regular user
```

### Step 6: Verify User Deleted
```
GET /api/admin/users
(with admin token)
→ Should only see admin user now
```

---

## 🎯 Quick Test Data

### Admin User
```json
{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "admin123456",
    "password_confirmation": "admin123456"
}
```

### Another Admin
```json
{
    "name": "Super Admin",
    "email": "superadmin@example.com",
    "password": "super123456",
    "password_confirmation": "super123456"
}
```

### Test Login (Wrong Password)
```json
{
    "email": "admin@example.com",
    "password": "wrongpassword"
}
```

---

## 🛡️ Features

✅ Separate admin authentication endpoints
✅ Admin can view all users
✅ Admin can update any user
✅ Admin can delete users (except themselves)
✅ Token-based authentication with Sanctum
✅ Located in `src/Admin/` directory (custom structure)
✅ Uses `Src\Admin` namespace

---

## 🔍 Error Handling

### Cannot Delete Self (403)
```json
{
    "message": "You cannot delete your own account"
}
```

### User Not Found (404)
```json
{
    "message": "No query results for model [App\\Models\\User] 999"
}
```

### Validation Error (422)
```json
{
    "message": "The email field is required.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

---

## 💡 Pro Tips

1. **Keep tokens separate:**
   - Admin token for `/api/admin/*` routes
   - User token for `/api/*` routes

2. **Test user deletion protection:**
   - Try deleting your own admin account
   - Should return 403 error

3. **In Postman, create separate folders:**
   - "User Auth" folder
   - "Admin Auth" folder

---

**Ready to test!** Start the server with `php artisan serve` and begin testing! 🚀
