# Src Directory Structure

This directory contains custom application code organized by responsibility.

## Directory Structure

```
src/
├── Services/       # Business logic and service classes
├── Repositories/   # Data access layer (repository pattern)
├── Helpers/        # Helper functions and utility classes
└── Traits/         # Reusable trait classes
```

## Usage

All classes in this directory use the `Src\` namespace.

### Example Service Class

```php
<?php

namespace Src\Services;

class UserService
{
    public function createUser(array $data)
    {
        // Business logic here
    }
}
```

### Example Repository Class

```php
<?php

namespace Src\Repositories;

use App\Models\User;

class UserRepository
{
    public function findById(int $id)
    {
        return User::find($id);
    }
}
```

### Example Helper Class

```php
<?php

namespace Src\Helpers;

class StringHelper
{
    public static function slugify(string $text): string
    {
        // Helper logic here
    }
}
```

### Example Trait

```php
<?php

namespace Src\Traits;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
```

## Integration with Laravel

The `src/` directory is autoloaded via composer.json:

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Src\\": "src/"
    }
}
```

After adding new classes, run: `composer dump-autoload`
