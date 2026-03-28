# Laravel

## Enums

Enums are of three types.

### Pure Enums

- Pure enums don't relate to any model and don't have any return type
- You can use `->name` to get the name, but you can't use `->value`
- Cannot be stored inside the database

### Backed Enums (string and int)

- Backed enums relate to a model and have a return type of string or int
- You can use `->name`, `->value`, `->from()`, `->tryFrom()`
- Backed Enum strings — stored in DB as `VARCHAR` or native `ENUM` in PostgreSQL
- Backed Enum int — stored in DB as `INTEGER`
- Strings can only be sorted alphabetically; ints can be sorted numerically

---

## Match

- `match` is a modernised version of `switch` — it returns a value directly (it's an expression, not a statement)
- No fall-through, no `break` needed — each arm is isolated
- Uses strict `===` comparison, not loose `==` like switch
- Throws `UnhandledMatchError` if no arm matches — forces you to handle every case
- Multiple conditions can share one arm using a comma
- Uses associative array convention `=>`

```php
public function label(): string {
    return match($this) {
        self::PENDING     => 'Pending',
        self::IN_PROGRESS => 'In Progress',
        self::COMPLETED   => 'Completed',
    };
}
```

---

## Namespace

- A namespace is a **postal address** for your class — it tells PHP where the class lives
- Prevents name collisions when two packages define a class with the same name

```php
namespace App\Models;

class User { }
// Full address: App\Models\User
```

---

## use

- `use` is a shortcut — import a class's full address once, then refer to it by its short name
- Has three distinct contexts: importing a class, importing a trait inside a class, and aliasing to avoid collisions

```php
// 1. Importing a class
use App\Models\User;

// 2. Importing a trait inside a class
class Order {
    use SoftDeletes;
}

// 3. Aliasing to avoid name collision
use App\Models\User as AppUser;
use Admin\Models\User as AdminUser;
```

---

## Facades

- Facades are **static shortcuts to services that Laravel manages internally** (the service container) — they are not packages
- Under the hood they are not truly static — the facade resolves the real service instance and forwards the call. It's syntactic sugar
- Imported with `use` like any class, then called with `::` static syntax

```php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Cache::get('key');
DB::table('users')->get();
Auth::user();
```

---

## Traits

- Traits are **reusable chunks of methods** you mix into a class — PHP's way around the lack of multiple inheritance
- A class can `use` multiple traits; the trait's methods become part of the class as if written directly there
- Laravel uses traits heavily: `SoftDeletes`, `HasFactory`, `Notifiable` are all traits

```php
trait HasTimestamps {
    public function getCreatedAt() {
        return $this->created_at->format('d M Y');
    }
}

class Order {
    use SoftDeletes;
    use HasTimestamps;
}
```

---

## How they all connect

```php
namespace App\Models;            

use Illuminate\Database\Eloquent\Model;        
use Illuminate\Database\Eloquent\SoftDeletes;  
use Illuminate\Support\Facades\Cache;          

class Order extends Model
{
    use SoftDeletes;             

    public function getCached()
    {
        return Cache::get('order'); 
    }
}
```
