# Laravel Cookie-Based Authentication - Setup Complete ✅

## What Was Fixed

### Issue:
"Session store not set on request" error when trying to register/login

### Solution:
Added `web` middleware to Profile routes to enable session support

## Current Structure

```
src/Admin/Profile/
├── Controllers/
│   └── AuthController.php    (Cookie-based auth with sessions)
├── Requests/
│   ├── LoginRequest.php      (Validation)
│   └── RegisterRequest.php   (Validation)
└── api.php                   (Routes with auth middleware)
```

## Routes Configuration

### Main Routes File: `routes/api.php`
```php
Route::prefix('admin')->group(function () {
    Route::prefix('profile')->middleware(['web'])->group(function () {
        require __DIR__ . '/../src/Admin/Profile/api.php';
    });
});
```

**Key:** `middleware(['web'])` enables sessions and cookies!

### Profile Routes: `src/Admin/Profile/api.php`
- Uses `auth` middleware (not `auth:sanctum`)
- Inherits `web` middleware from parent group

## Active Endpoints

| Endpoint | Method | Auth | CSRF | Description |
|----------|--------|------|------|-------------|
| `/api/admin/profile/csrf-token` | GET | No | No | Get CSRF token |
| `/api/admin/profile/register` | POST | No | Yes | Register admin |
| `/api/admin/profile/login` | POST | No | Yes | Login admin |
| `/api/admin/profile/profile` | GET | Yes | No | Get profile |
| `/api/admin/profile/profile` | PUT | Yes | Yes | Update profile |
| `/api/admin/profile/profile` | DELETE | Yes | Yes | Delete profile |
| `/api/admin/profile/logout` | POST | Yes | Yes | Logout |

## Testing in Postman

### 1. Get CSRF Token
```
GET http://127.0.0.1:8000/api/admin/profile/csrf-token
```

### 2. Register (with CSRF)
```
POST http://127.0.0.1:8000/api/admin/profile/register

Headers:
X-CSRF-TOKEN: your_token
Content-Type: application/json

Body:
{
    "name": "Test Admin",
    "email": "admin@test.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### 3. Check Cookies
After register/login, check "Cookies" in Postman:
- Should see `laravel_session` cookie
- Should see `XSRF-TOKEN` cookie

### 4. Get Profile (auto uses cookie)
```
GET http://127.0.0.1:8000/api/admin/profile/profile

Headers:
Accept: application/json
```

## Configuration Files

### `.env`
```env
SESSION_DRIVER=database
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1,127.0.0.1:8000,localhost:8000
```

### `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->statefulApi();
    $middleware->validateCsrfTokens(except: []);
})
```

## Important Notes

✅ **Session Storage:** Using database sessions (check `sessions` table)
✅ **CSRF Protection:** Enabled for all POST/PUT/DELETE
✅ **Cookies:** Automatically handled by Laravel
✅ **Middleware:** `web` middleware provides session support

## Troubleshooting

### Error: "Session store not set"
- Make sure `web` middleware is applied to routes
- Check `bootstrap/app.php` has `statefulApi()`

### Error: "CSRF token mismatch"
- Get fresh token from `/csrf-token` endpoint
- Include `X-CSRF-TOKEN` header

### Error: "Unauthenticated"
- Make sure you logged in first
- Check cookie is present in Postman
- Session may have expired (default: 120 min)

## Files Modified

1. ✅ `routes/api.php` - Added `web` middleware to profile routes
2. ✅ `src/Admin/Profile/api.php` - Changed to `auth` middleware
3. ✅ `src/Admin/Profile/Controllers/AuthController.php` - Cookie-based auth
4. ✅ `bootstrap/app.php` - Added `statefulApi()`
5. ✅ `.env` - Added `SANCTUM_STATEFUL_DOMAINS`

## Verification Commands

```bash
# View routes
php artisan route:list --path=admin/profile

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Start server
php artisan serve
```

## Ready to Test! 🚀

All configuration is complete. Test with Postman following the guide in `COOKIE_AUTH_GUIDE.md`.
