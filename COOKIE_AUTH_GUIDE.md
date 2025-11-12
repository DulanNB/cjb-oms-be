# Cookie-Based Authentication with CSRF Token - Testing Guide

## 🔐 Overview

The Admin Profile module now uses **cookie-based session authentication** with **CSRF token protection** instead of Bearer tokens.

## 🔧 Configuration

### Session Settings (Already Configured)
- Session Driver: `database`
- Session Lifetime: `120 minutes`
- Stateful API enabled in `bootstrap/app.php`
- SANCTUM_STATEFUL_DOMAINS configured

## 📋 How It Works

1. **Get CSRF Token** first (for CSRF protection)
2. **Login/Register** with credentials (receives session cookie)
3. **Use session cookie** for authenticated requests
4. **Include CSRF token** in headers for state-changing operations

---

## 🧪 Postman Testing Steps

### Step 1: Get CSRF Token

**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/csrf-token`

**Headers:**
```
Accept: application/json
```

**Response:**
```json
{
    "csrf_token": "abcdefgh123456..."
}
```

**Important:** Copy this token for use in POST/PUT/DELETE requests.

---

### Step 2: Register Admin Profile

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/register`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN_HERE
```

**Body (JSON):**
```json
{
    "name": "Admin Cookie User",
    "email": "admincookie@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "message": "Admin registration successful",
    "user": {
        "name": "Admin Cookie User",
        "email": "admincookie@example.com",
        "updated_at": "2025-11-05T17:00:00.000000Z",
        "created_at": "2025-11-05T17:00:00.000000Z",
        "id": 1
    }
}
```

**Note:** Postman automatically stores the session cookie (laravel_session).

---

### Step 3: Login

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/login`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN_HERE
```

**Body (JSON):**
```json
{
    "email": "admincookie@example.com",
    "password": "password123"
}
```

**Optional - Remember Me:**
```json
{
    "email": "admincookie@example.com",
    "password": "password123",
    "remember": true
}
```

**Response (200):**
```json
{
    "message": "Admin login successful",
    "user": {
        "id": 1,
        "name": "Admin Cookie User",
        "email": "admincookie@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T17:00:00.000000Z",
        "updated_at": "2025-11-05T17:00:00.000000Z"
    }
}
```

---

### Step 4: Get Profile (Protected)

**Method:** `GET`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Accept: application/json
```

**Note:** Cookie is automatically sent by Postman.

**Response (200):**
```json
{
    "profile": {
        "id": 1,
        "name": "Admin Cookie User",
        "email": "admincookie@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T17:00:00.000000Z",
        "updated_at": "2025-11-05T17:00:00.000000Z"
    }
}
```

---

### Step 5: Update Profile (Protected)

**Method:** `PUT`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN_HERE
```

**Body (JSON) - Update Name:**
```json
{
    "name": "Updated Admin Name"
}
```

**Response (200):**
```json
{
    "message": "Profile updated successfully",
    "user": {
        "id": 1,
        "name": "Updated Admin Name",
        "email": "admincookie@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T17:00:00.000000Z",
        "updated_at": "2025-11-05T17:05:00.000000Z"
    }
}
```

---

### Step 6: Logout (Protected)

**Method:** `POST`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/logout`

**Headers:**
```
Accept: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN_HERE
```

**Response (200):**
```json
{
    "message": "Admin logged out successfully"
}
```

**Note:** Session cookie will be invalidated.

---

### Step 7: Delete Profile (Protected)

**Method:** `DELETE`  
**URL:** `http://127.0.0.1:8000/api/admin/profile/profile`

**Headers:**
```
Accept: application/json
X-CSRF-TOKEN: YOUR_CSRF_TOKEN_HERE
```

**Response (200):**
```json
{
    "message": "Profile deleted successfully"
}
```

---

## 🔑 Important Headers

### For GET Requests:
```
Accept: application/json
```

### For POST/PUT/DELETE Requests:
```
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: your_csrf_token_here
```

---

## 📝 Postman Configuration

### Enable Cookie Storage:
1. Go to **Settings** (gear icon)
2. Enable **"Automatically follow redirects"**
3. Enable **"Send cookies"**

### View Cookies:
1. Click **"Cookies"** below the Send button
2. You should see `laravel_session` cookie after login

### Using CSRF Token:
1. Get CSRF token from `/csrf-token` endpoint
2. Add header: `X-CSRF-TOKEN: token_value`
3. Or use Postman variable:
   - Save CSRF token: `pm.environment.set("csrf_token", jsonData.csrf_token);`
   - Use it: `{{csrf_token}}`

---

## 🧪 Testing with Postman - Complete Flow

### Setup:
1. Create new Collection: "Admin Profile Cookie Auth"
2. Create Environment variable: `csrf_token`

### Test Flow:

#### 1. Get CSRF Token
```
GET /api/admin/profile/csrf-token
Save response to environment variable
```

#### 2. Register
```
POST /api/admin/profile/register
Headers: X-CSRF-TOKEN: {{csrf_token}}
Body: name, email, password, password_confirmation
```

#### 3. Check Cookies
```
Click "Cookies" → Should see "laravel_session"
```

#### 4. Get Profile
```
GET /api/admin/profile/profile
Cookie is automatically sent
```

#### 5. Update Profile
```
PUT /api/admin/profile/profile
Headers: X-CSRF-TOKEN: {{csrf_token}}
Body: name/email/password to update
```

#### 6. Logout
```
POST /api/admin/profile/logout
Headers: X-CSRF-TOKEN: {{csrf_token}}
Session invalidated
```

---

## 🚨 Common Issues & Solutions

### Issue: "CSRF token mismatch"
**Solution:** 
1. Get fresh CSRF token
2. Make sure `X-CSRF-TOKEN` header is included
3. Check cookie is being sent

### Issue: "Unauthenticated" (401)
**Solution:**
1. Login first to get session cookie
2. Make sure cookies are enabled in Postman
3. Check session hasn't expired (120 min)

### Issue: "Session expired"
**Solution:**
1. Login again to get new session
2. Increase SESSION_LIFETIME in .env if needed

### Issue: Cookie not being saved
**Solution:**
1. Enable cookie storage in Postman settings
2. Make sure domain matches (127.0.0.1 or localhost)
3. Check APP_URL in .env matches your request URL

---

## 🔐 Security Features

✅ **CSRF Protection** - All state-changing operations require CSRF token
✅ **Session-based Auth** - More secure than stateless tokens for same-origin
✅ **Session Regeneration** - New session ID on login/logout prevents fixation
✅ **Cookie-based** - HttpOnly cookies (more secure than localStorage)
✅ **Session Invalidation** - Proper cleanup on logout and account deletion

---

## 📊 API Endpoints Summary

| Endpoint | Method | Auth | CSRF Required | Description |
|----------|--------|------|---------------|-------------|
| /api/admin/profile/csrf-token | GET | No | No | Get CSRF token |
| /api/admin/profile/register | POST | No | Yes | Register |
| /api/admin/profile/login | POST | No | Yes | Login |
| /api/admin/profile/profile | GET | Yes | No | Get profile |
| /api/admin/profile/profile | PUT | Yes | Yes | Update profile |
| /api/admin/profile/profile | DELETE | Yes | Yes | Delete profile |
| /api/admin/profile/logout | POST | Yes | Yes | Logout |

---

## 💡 Advanced Postman Setup

### Auto-save CSRF Token (Tests Tab):
Add to GET /csrf-token request:
```javascript
var jsonData = pm.response.json();
pm.environment.set("csrf_token", jsonData.csrf_token);
console.log("CSRF Token saved:", jsonData.csrf_token);
```

### Check Authentication Status:
Add to any protected route:
```javascript
pm.test("User is authenticated", function () {
    pm.response.to.have.status(200);
    pm.response.to.not.have.status(401);
});
```

### Pre-request Script (Auto get CSRF):
```javascript
// Get CSRF token before each request
if (!pm.environment.get("csrf_token")) {
    pm.sendRequest({
        url: 'http://127.0.0.1:8000/api/admin/profile/csrf-token',
        method: 'GET',
    }, function (err, response) {
        var data = response.json();
        pm.environment.set("csrf_token", data.csrf_token);
    });
}
```

---

## 🔄 Difference from Token-Based Auth

### Before (Token-Based):
- Get token after login
- Send `Authorization: Bearer TOKEN` header
- Token stored in variable/localStorage
- Stateless

### Now (Cookie-Based):
- Get session cookie after login
- Cookie automatically sent by browser/Postman
- CSRF token required for mutations
- Stateful (session stored on server)

---

## 🚀 Start Testing

```bash
# Start server
php artisan serve

# Clear cache (if needed)
php artisan config:clear
php artisan cache:clear

# View routes
php artisan route:list --path=admin/profile
```

**Ready to test with cookies and CSRF!** 🍪🔒
