# Cookie & CSRF Authentication - Quick Reference

## ✅ What Changed

The Admin Profile authentication now uses **cookies and CSRF tokens** instead of Bearer tokens.

### Key Changes:
1. ✅ Session/cookie-based authentication
2. ✅ CSRF token protection
3. ✅ `auth:web` middleware instead of `auth:sanctum`
4. ✅ Session regeneration on login/logout
5. ✅ Stateful API enabled

## 🚀 Quick Test in Postman

### 1. Get CSRF Token
```
GET http://127.0.0.1:8000/api/admin/profile/csrf-token
```
Copy the `csrf_token` from response.

### 2. Login
```
POST http://127.0.0.1:8000/api/admin/profile/login

Headers:
X-CSRF-TOKEN: your_token_here
Content-Type: application/json

Body:
{
    "email": "admin@example.com",
    "password": "password123"
}
```

### 3. Check Cookies
Click "Cookies" in Postman → Should see `laravel_session` cookie

### 4. Get Profile (Uses Cookie)
```
GET http://127.0.0.1:8000/api/admin/profile/profile

Headers:
Accept: application/json
```
Cookie is sent automatically!

## 📝 Important Notes

### CSRF Token Required For:
- ✅ POST (register, login, logout)
- ✅ PUT (update profile)
- ✅ DELETE (delete profile)

### CSRF Token NOT Required For:
- ❌ GET requests (csrf-token, get profile)

### Headers to Use:
```
X-CSRF-TOKEN: your_csrf_token_here
Content-Type: application/json
Accept: application/json
```

## 🔐 Security Features

- **CSRF Protection**: Prevents cross-site request forgery
- **Cookie-based**: More secure than localStorage
- **Session Regeneration**: Prevents session fixation
- **Stateful API**: `statefulApi()` enabled in bootstrap/app.php

## 📄 Files Modified

1. `src/Admin/Profile/Controllers/AuthController.php` - Uses Auth::login() and sessions
2. `src/Admin/Profile/api.php` - Uses auth:web middleware and added CSRF route
3. `bootstrap/app.php` - Added statefulApi() and CSRF configuration
4. `.env` - Added SANCTUM_STATEFUL_DOMAINS

## 📖 Full Documentation

See **COOKIE_AUTH_GUIDE.md** for complete testing instructions!

## 🎯 Test Flow

1. Get CSRF token → Save it
2. Register/Login → Get session cookie
3. Use cookie for authenticated requests
4. Include CSRF token in POST/PUT/DELETE
5. Logout → Session invalidated

---

**Remember:** Make sure cookies are enabled in Postman settings!
