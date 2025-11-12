# Admin & Organization Authentication System

## Overview

The system has been updated to use separate `admins` and `organizations` tables for authentication and multi-tenancy support. Each admin registration now automatically creates a new organization.

## Database Schema

### Organizations Table
- `id` - Primary key
- `name` - Organization name
- `slug` - Unique URL-friendly identifier (auto-generated from name)
- `email` - Organization contact email
- `phone` - Organization phone number
- `address` - Full address
- `city` - City
- `state` - State/Province
- `postal_code` - Postal/ZIP code
- `country` - Country
- `website` - Organization website
- `logo` - Logo file path
- `is_active` - Active status (boolean)
- `created_at`, `updated_at`, `deleted_at` - Timestamps

### Admins Table
- `id` - Primary key
- `organization_id` - Foreign key to organizations
- `first_name` - Admin first name
- `last_name` - Admin last name
- `email` - Unique email address
- `password` - Hashed password
- `phone` - Phone number
- `avatar` - Avatar file path
- `is_active` - Active status (boolean)
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember token for sessions
- `created_at`, `updated_at`, `deleted_at` - Timestamps

## Authentication Guard

### Configuration
The system uses a separate `admin` guard configured in `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',
    ],
],
```

### Sanctum Configuration
The admin guard is included in Sanctum's stateful authentication:

```php
'guard' => ['web', 'admin'],
```

## API Endpoints

### Registration
**POST** `/api/admin/profile/register`

Creates both a new organization and admin account.

**Request Body:**
```json
{
  "organization_name": "Acme Corp",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john@acme.com",
  "password": "SecurePass123",
  "password_confirmation": "SecurePass123",
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "message": "Admin registration successful",
  "user": {
    "id": 1,
    "organization_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@acme.com",
    "phone": "+1234567890",
    "is_active": true,
    "organization": {
      "id": 1,
      "name": "Acme Corp",
      "slug": "acme-corp",
      "email": "john@acme.com",
      "is_active": true
    }
  }
}
```

### Login
**POST** `/api/admin/profile/login`

**Request Body:**
```json
{
  "email": "john@acme.com",
  "password": "SecurePass123",
  "remember": true
}
```

**Response:**
```json
{
  "message": "Admin login successful",
  "user": {
    "id": 1,
    "organization_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@acme.com",
    "full_name": "John Doe",
    "organization": {
      "id": 1,
      "name": "Acme Corp",
      "slug": "acme-corp"
    }
  }
}
```

### Get Profile
**GET** `/api/admin/profile`

Requires authentication.

**Response:**
```json
{
  "data": {
    "id": 1,
    "organization_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john@acme.com",
    "organization": {
      "id": 1,
      "name": "Acme Corp",
      "slug": "acme-corp"
    }
  }
}
```

### Update Profile
**PUT** `/api/admin/profile`

Requires authentication.

**Request Body:**
```json
{
  "first_name": "John",
  "last_name": "Smith",
  "email": "john.smith@acme.com",
  "phone": "+1234567890",
  "password": "NewSecurePass123",
  "password_confirmation": "NewSecurePass123"
}
```

### Logout
**POST** `/api/admin/profile/logout`

Requires authentication.

### Delete Profile
**DELETE** `/api/admin/profile`

Requires authentication. Performs a soft delete.

## Models

### Admin Model
Located at `app/Models/Admin.php`

**Features:**
- Extends `Illuminate\Foundation\Auth\User` (Authenticatable)
- Uses `HasApiTokens`, `Notifiable`, `SoftDeletes` traits
- Password automatically hashed
- Includes `full_name` accessor
- Has relationship with Organization

**Relationships:**
- `organization()` - BelongsTo Organization

**Scopes:**
- `active()` - Filter active admins
- `forOrganization($id)` - Filter by organization

### Organization Model
Located at `app/Models/Organization.php`

**Features:**
- Uses `SoftDeletes` trait
- Auto-generates unique slug from name
- Active status tracking

**Relationships:**
- `admins()` - HasMany Admin
- `orders()` - HasMany Order

**Scopes:**
- `active()` - Filter active organizations

## Usage in Controllers

### Getting Current Admin
```php
$admin = Auth::guard('admin')->user();
$organizationId = $admin->organization_id;
$organization = $admin->organization;
```

### Checking Authentication
```php
if (Auth::guard('admin')->check()) {
    // Admin is authenticated
}
```

### Manual Login
```php
Auth::guard('admin')->login($admin);
```

### Logout
```php
Auth::guard('admin')->logout();
```

## Migration Commands

To run the migrations:
```bash
php artisan migrate
```

To rollback:
```bash
php artisan migrate:rollback
```

To refresh (drop all tables and re-run):
```bash
php artisan migrate:fresh
```

## Security Features

1. **Password Hashing**: Automatic via Laravel's `hashed` cast
2. **CSRF Protection**: Required for all state-changing operations
3. **Session Regeneration**: Prevents session fixation attacks
4. **Soft Deletes**: Allows data recovery
5. **Active Status Checks**: Both admin and organization must be active
6. **Database Transactions**: Registration uses transactions for data integrity
7. **Guard Isolation**: Admin and user sessions are separate

## Multi-Tenancy Support

The organization_id in the admins table enables multi-tenancy:

```php
// Scope queries to current organization
$orders = Order::where('organization_id', Auth::guard('admin')->user()->organization_id)->get();

// Or using relationship
$orders = Auth::guard('admin')->user()->organization->orders;
```

## Frontend Integration

### Registration Flow
1. User fills organization name, personal details, and credentials
2. Frontend sends POST request to `/api/admin/profile/register`
3. Backend creates organization and admin atomically
4. Admin is automatically logged in
5. Frontend stores session and redirects to dashboard

### Login Flow
1. User enters email and password
2. Frontend sends POST request to `/api/admin/profile/login`
3. Backend validates credentials and active status
4. Session is established
5. Frontend receives user data including organization
6. Frontend stores session and redirects to dashboard

### Authenticated Requests
All subsequent requests include:
- Session cookie (automatic)
- CSRF token in header: `X-XSRF-TOKEN`

## Notes

- Organization slug is automatically generated and ensured to be unique
- Both admin and organization must be active for login to succeed
- All admin endpoints use the `admin` guard
- The system maintains backward compatibility with the `web` guard for regular users
- Registration creates both organization and admin in a single transaction
