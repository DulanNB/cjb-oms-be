# Postman Testing Guide - Laravel Sanctum Authentication

Server is running at: **http://127.0.0.1:8000**

---

## 📋 Test 1: REGISTER NEW USER

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/register`

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON - raw)
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Expected Response (201 Created)
```json
{
    "message": "Registration successful",
    "user": {
        "name": "John Doe",
        "email": "john@example.com",
        "updated_at": "2025-11-05T15:45:00.000000Z",
        "created_at": "2025-11-05T15:45:00.000000Z",
        "id": 1
    },
    "access_token": "1|abcd1234efgh5678ijkl9012mnop3456qrst7890",
    "token_type": "Bearer"
}
```

**⚠️ IMPORTANT:** Copy the `access_token` from the response - you'll need it for protected routes!

---

## 📋 Test 2: LOGIN EXISTING USER

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/login`

### Headers
```
Content-Type: application/json
Accept: application/json
```

### Body (JSON - raw)
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

### Expected Response (200 OK)
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T15:45:00.000000Z",
        "updated_at": "2025-11-05T15:45:00.000000Z"
    },
    "access_token": "2|xyz9876abc5432def1098ghi7654jkl3210",
    "token_type": "Bearer"
}
```

**⚠️ IMPORTANT:** Copy this new `access_token` for the next tests!

---

## 📋 Test 3: GET AUTHENTICATED USER (Protected Route)

### Request Setup
- **Method:** `GET`
- **URL:** `http://127.0.0.1:8000/api/user`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ACCESS_TOKEN_HERE
```

**🔑 Replace `YOUR_ACCESS_TOKEN_HERE` with the token from login/register response**

Example:
```
Authorization: Bearer 2|xyz9876abc5432def1098ghi7654jkl3210
```

### Body
No body needed

### Expected Response (200 OK)
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": null,
        "created_at": "2025-11-05T15:45:00.000000Z",
        "updated_at": "2025-11-05T15:45:00.000000Z"
    }
}
```

---

## 📋 Test 4: LOGOUT (Protected Route)

### Request Setup
- **Method:** `POST`
- **URL:** `http://127.0.0.1:8000/api/logout`

### Headers
```
Accept: application/json
Authorization: Bearer YOUR_ACCESS_TOKEN_HERE
```

### Body
No body needed

### Expected Response (200 OK)
```json
{
    "message": "Logged out successfully"
}
```

**Note:** After logout, the token will be invalidated and cannot be used again.

---

## 🎯 Additional Test Cases

### Test 5: Register Another User
```json
{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "securepass456",
    "password_confirmation": "securepass456"
}
```

### Test 6: Register with Invalid Data (Should Fail - 422)
```json
{
    "name": "Test User",
    "email": "invalid-email",
    "password": "123",
    "password_confirmation": "456"
}
```

### Test 7: Login with Wrong Password (Should Fail - 422)
```json
{
    "email": "john@example.com",
    "password": "wrongpassword"
}
```

### Test 8: Access Protected Route Without Token (Should Fail - 401)
- Try accessing `/api/user` without Authorization header

---

## 📝 Step-by-Step Instructions for Postman

### 1. CREATE COLLECTION
1. Open Postman
2. Click "New" → "Collection"
3. Name it "Laravel Sanctum Auth"

### 2. ADD REGISTER REQUEST
1. Click "Add Request" in your collection
2. Name: "Register"
3. Set method to `POST`
4. URL: `http://127.0.0.1:8000/api/register`
5. Go to **Headers** tab:
   - Add: `Content-Type` = `application/json`
   - Add: `Accept` = `application/json`
6. Go to **Body** tab:
   - Select `raw`
   - Choose `JSON` from dropdown
   - Paste the register JSON data
7. Click **Send**

### 3. ADD LOGIN REQUEST
1. Add new request: "Login"
2. Method: `POST`
3. URL: `http://127.0.0.1:8000/api/login`
4. Headers: Same as above
5. Body: Paste login JSON data
6. Click **Send**
7. **COPY THE TOKEN** from response

### 4. ADD GET USER REQUEST
1. Add new request: "Get User"
2. Method: `GET`
3. URL: `http://127.0.0.1:8000/api/user`
4. Headers:
   - Add: `Accept` = `application/json`
   - Add: `Authorization` = `Bearer YOUR_TOKEN_HERE`
5. No body needed
6. Click **Send**

### 5. ADD LOGOUT REQUEST
1. Add new request: "Logout"
2. Method: `POST`
3. URL: `http://127.0.0.1:8000/api/logout`
4. Headers: Same as Get User
5. Click **Send**

---

## 🔍 Troubleshooting

### Error: "Connection refused"
- Make sure Laravel server is running: `php artisan serve`

### Error: "Unauthenticated" (401)
- Check if you included `Authorization` header
- Verify token format: `Bearer {token}` (note the space)
- Token might be expired or invalid

### Error: "The email has already been taken"
- User already exists, try login instead
- Or use a different email address

### Error: "SQLSTATE[HY000] [2002]"
- Check MySQL is running
- Verify database credentials in `.env`

---

## 💡 Pro Tips

1. **Use Postman Variables:**
   - Save token as environment variable
   - Use `{{token}}` in Authorization header

2. **Test Script (Auto-save token):**
   Add this to Register/Login request "Tests" tab:
   ```javascript
   var jsonData = pm.response.json();
   pm.environment.set("token", jsonData.access_token);
   ```
   Then use `{{token}}` in Authorization header

3. **Quick Test All:**
   - Register → Copy token
   - Get User (with token)
   - Logout (with same token)
   - Try Get User again (should fail)
   - Login → Get new token
   - Get User (with new token - should work)

---

## 📊 Expected Results Summary

| Endpoint | Method | Auth Required | Success Code |
|----------|--------|---------------|--------------|
| /api/register | POST | No | 201 |
| /api/login | POST | No | 200 |
| /api/user | GET | Yes | 200 |
| /api/logout | POST | Yes | 200 |

---

**Server Status:** ✅ Running at http://127.0.0.1:8000

Ready to test! Start with Test 1 (Register) and work your way through. 🚀
