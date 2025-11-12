# Laravel Sanctum Authentication API

Complete authentication system with registration, login, logout, and user profile endpoints.

## Setup Completed

✅ MySQL database configured (`cjboms`)
✅ Laravel Sanctum installed and configured
✅ Database migrations executed
✅ API routes configured
✅ Authentication controller created

## Database Configuration

Database: **cjboms**
Connection: **mysql** (127.0.0.1:3306)

## API Endpoints

Base URL: `http://localhost:8000/api`

### 1. Register New User

**POST** `/api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201 Created):**
```json
{
    "message": "Registration successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-11-05T15:30:00.000000Z",
        "updated_at": "2025-11-05T15:30:00.000000Z"
    },
    "access_token": "1|abcdefghijklmnopqrstuvwxyz...",
    "token_type": "Bearer"
}
```

### 2. Login

**POST** `/api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-11-05T15:30:00.000000Z",
        "updated_at": "2025-11-05T15:30:00.000000Z"
    },
    "access_token": "2|zyxwvutsrqponmlkjihgfedcba...",
    "token_type": "Bearer"
}
```

### 3. Get Authenticated User

**GET** `/api/user`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200 OK):**
```json
{
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "created_at": "2025-11-05T15:30:00.000000Z",
        "updated_at": "2025-11-05T15:30:00.000000Z"
    }
}
```

### 4. Logout

**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {access_token}
```

**Response (200 OK):**
```json
{
    "message": "Logged out successfully"
}
```

## Testing with cURL

### Register
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"name\":\"John Doe\",\"email\":\"john@example.com\",\"password\":\"password123\",\"password_confirmation\":\"password123\"}"
```

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d "{\"email\":\"john@example.com\",\"password\":\"password123\"}"
```

### Get User (Protected Route)
```bash
curl -X GET http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Logout
```bash
curl -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Testing with Postman

1. **Register/Login**: Send POST request without token
2. **Copy the access_token** from response
3. **Protected Routes**: Add header `Authorization: Bearer {token}`

## How to Start the Server

```bash
php artisan serve
```

Server will run at: `http://localhost:8000`

## Validation Rules

### Registration
- `name`: required, string, max 255 characters
- `email`: required, valid email, unique in users table
- `password`: required, min 8 characters, must be confirmed

### Login
- `email`: required, valid email format
- `password`: required

## Error Responses

### Validation Error (422 Unprocessable Entity)
```json
{
    "message": "The email field is required. (and 1 more error)",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Error (422 Unprocessable Entity)
```json
{
    "message": "The provided credentials are incorrect.",
    "errors": {
        "email": ["The provided credentials are incorrect."]
    }
}
```

### Unauthorized (401 Unauthorized)
```json
{
    "message": "Unauthenticated."
}
```

## Security Features

- ✅ Password hashing with bcrypt
- ✅ Token-based authentication (Sanctum)
- ✅ Email validation and uniqueness check
- ✅ Password confirmation on registration
- ✅ Protected routes with auth:sanctum middleware
- ✅ Individual token revocation on logout

## Files Modified/Created

1. `.env` - MySQL configuration
2. `routes/api.php` - API routes (created)
3. `app/Http/Controllers/Api/AuthController.php` - Authentication controller
4. `app/Models/User.php` - Added HasApiTokens trait
5. `bootstrap/app.php` - Added API routing
6. Database migrations - Users and personal_access_tokens tables

## Next Steps

You can now:
- Start the Laravel server: `php artisan serve`
- Test the API endpoints using Postman or cURL
- Build your frontend application
- Add more protected routes as needed
