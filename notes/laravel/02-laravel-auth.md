# Laravel — Auth & Blade

---

## Blade Directives

Blade is Laravel's templating engine. Instead of writing raw PHP in your HTML files, Blade gives you clean shorthand directives that start with `@`.

- Blade files live in `resources/views/` and end in `.blade.php`
- Laravel compiles them into plain PHP behind the scenes — you never touch the compiled output
- Directives are just shortcuts — `@if` compiles to `<?php if(): ?>`, `@foreach` to `<?php foreach(): ?>` etc.

```blade
{{-- Output a variable (auto-escaped, safe from XSS) --}}
{{ $user->name }}

{{-- Basic conditionals --}}
@if($user->isAdmin())
    <p>Welcome admin</p>
@elseif($user->isEditor())
    <p>Welcome editor</p>
@else
    <p>Welcome guest</p>
@endif

{{-- Loops --}}
@foreach($posts as $post)
    <p>{{ $post->title }}</p>
@endforeach

{{-- Include another blade file --}}
@include('partials.navbar')
```

---

## @guest and @auth

These are Blade directives that check whether the current visitor is logged in or not. Laravel knows this from the session.

- `@guest` — renders its content only if the user is **not** logged in
- `@auth` — renders its content only if the user **is** logged in
- Defined by Laravel internally — wired to `Auth::check()` under the hood

```blade
@guest
    <a href="/login">Login</a>
    <a href="/register">Register</a>
@endguest

@auth
    <span>Hello, {{ Auth::user()->name }}</span>
    <a href="/logout">Logout</a>
@endauth
```

Think of them as `@if(!auth()->check())` and `@if(auth()->check())` — just cleaner.

---

## Middleware

Middleware is code that runs **between** the incoming request and your controller. It's a gatekeeper — it checks something before letting the request through.

- Defined in `app/Http/Middleware/`
- Registered in `bootstrap/app.php` (Laravel 11) or `app/Http/Kernel.php` (Laravel 10 and below)
- Applied to routes using `->middleware('name')`

### ->middleware('guest')

Redirects the user **away** if they are already logged in. Used on login/register routes — no point showing a login page to someone already logged in.

```php
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest');
```

If a logged-in user hits `/login`, Laravel redirects them to `/dashboard` (or wherever `RedirectIfAuthenticated` is configured).

### ->middleware('auth')

Redirects the user **away** if they are **not** logged in. Used to protect pages that require a login.

```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth');

Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth');
```

If a guest hits `/dashboard`, Laravel redirects them to `/login`.

---

## Auth::login() and Auth::logout()

These are the two core methods that actually log a user in and out. They live in the `Auth` facade — which is a shortcut to Laravel's authentication service.

- Defined in `Illuminate\Support\Facades\Auth`
- The facade talks to the auth guard, which manages the session

### Auth::login()

Logs a user in by storing their identity in the session.

```php
use Illuminate\Support\Facades\Auth;

$user = User::where('email', $request->email)->first();

if ($user && Hash::check($request->password, $user->password)) {
    Auth::login($user);                    // logs the user in
    Auth::login($user, remember: true);    // logs in + sets remember me cookie
    return redirect('/dashboard');
}
```

### Auth::logout()

Clears the user's session and logs them out.

```php
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate();       // destroy the session
    $request->session()->regenerateToken();  // regenerate CSRF token (security)

    return redirect('/login');
}
```

Always invalidate the session and regenerate the CSRF token after logout — without this, the old session could be replayed.

---

## redirect()->intended() and redirect()->back()

Both are redirect helpers — they decide **where to send the user** after an action.

### redirect()->intended()

When a guest tries to visit a protected page (e.g. `/dashboard`), Laravel saves that URL. After they log in, `intended()` sends them back to where they were originally trying to go.

```php
public function login(Request $request)
{
    // ... validate and check credentials ...

    Auth::login($user);

    return redirect()->intended('/dashboard');
    // goes to the saved URL, or /dashboard if none was saved
}
```

- The fallback (`'/dashboard'`) is used when there's no saved intended URL
- The intended URL is stored in the session by the `auth` middleware automatically

### redirect()->back()

Simply sends the user back to the previous page — like hitting the browser's back button.

```php
return redirect()->back();

// back() with errors flashed to the session
return redirect()->back()->withErrors($validator)->withInput();
```

Common use: when a form fails validation, send the user back to the form with their input preserved and errors shown.

---

## ->with('key', 'value') — Flash Messages

`->with()` flashes a value into the session for exactly one request. It disappears after being read — perfect for one-time success or error messages.

```php
return redirect('/dashboard')->with('success', 'You are now logged in!');

return redirect()->back()->with('error', 'Invalid credentials.');
```

Reading it in Blade:

```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif
```

The key (`'success'`, `'error'`) is just a name you choose — Laravel doesn't enforce any specific names.

---

## $request->validate()

Validates incoming form data. If validation fails, Laravel automatically redirects back with errors and the old input — you don't handle the failure yourself.

- Defined on the `Request` object, available in every controller method
- Rules are passed as an array — key is the field name, value is the rules

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:8', 'confirmed'],
    ]);

    // only runs if validation passed
    User::create($validated);
}
```

On failure, Laravel:
1. Redirects back to the previous page
2. Flashes validation errors into the session
3. Flashes old input so fields can be repopulated

Reading errors in Blade:

```blade
<input type="email" name="email" value="{{ old('email') }}">

@error('email')
    <p class="error">{{ $message }}</p>
@enderror
```

- `old('email')` — repopulates the field with what the user typed
- `@error('field')` — renders its content if that field has a validation error

---

## Rule::unique

A more flexible way to write the `unique` validation rule. Used when you need to ignore a specific record (e.g. when updating a user's own email).

- Defined in `Illuminate\Validation\Rule`

```php
use Illuminate\Validation\Rule;

// Basic — email must be unique in the users table
'email' => ['required', 'email', Rule::unique('users', 'email')],

// When updating — ignore the current user's own record
'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
```

Why `ignore()`? Without it, when a user updates their profile without changing their email, validation would fail because that email already exists in the DB — belonging to them. `ignore()` tells Laravel to skip that row.

---

## Password::defaults()

A Laravel helper that gives you a standard set of password rules without listing them all manually every time.

- Defined in `Illuminate\Validation\Rules\Password`
- You configure the defaults once (usually in `AppServiceProvider`) and reuse everywhere

```php
// In AppServiceProvider::boot()
use Illuminate\Validation\Rules\Password;

Password::defaults(function () {
    return Password::min(8)
        ->mixedCase()   // upper + lowercase
        ->numbers()     // at least one number
        ->symbols()     // at least one symbol
        ->uncompromised(); // checks against known breached passwords
});
```

Then in validation, just reference it:

```php
$request->validate([
    'password' => ['required', 'confirmed', Password::defaults()],
]);
```

Instead of repeating `min:8|mixedCase|numbers|symbols` on every form that has a password field.

---

## How it all fits together — a login flow

```php
// routes/web.php
Route::get('/login',  [AuthController::class, 'showForm'])->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout',[AuthController::class, 'logout'])->middleware('auth');

// AuthController.php
public function login(Request $request)
{
    // 1. Validate input
    $request->validate([
        'email'    => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Attempt login
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // prevent session fixation
        return redirect()->intended('/dashboard')
            ->with('success', 'Welcome back!');
    }

    // 3. Failed — go back with error
    return redirect()->back()
        ->withErrors(['email' => 'These credentials do not match our records.'])
        ->withInput();
}

public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Logged out successfully.');
}
```

```blade
{{-- login.blade.php --}}
@guest
    @if(session('error'))
        <p>{{ session('error') }}</p>
    @endif

    <form method="POST" action="/login">
        @csrf
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p>{{ $message }}</p> @enderror

        <input type="password" name="password">
        @error('password') <p>{{ $message }}</p> @enderror

        <button type="submit">Login</button>
    </form>
@endguest
```

---

## Quick reference

| Concept                  | What it does                          | Where it lives                         |
|--------------------------|---------------------------------------|----------------------------------------|
| `@guest` / `@auth`       | Show content based on login state     | Blade directive, built into Laravel    |
| `->middleware('guest')`  | Block logged-in users from a route    | Route definition                       |
| `->middleware('auth')`   | Block guests from a route             | Route definition                       |
| `Auth::login($user)`     | Log a user in via session             | `Illuminate\Support\Facades\Auth`      |
| `Auth::logout()`         | Clear session, log out                | `Illuminate\Support\Facades\Auth`      |
| `redirect()->intended()` | Go to where the user was heading      | `Illuminate\Routing\Redirector`        |
| `redirect()->back()`     | Go to previous page                   | `Illuminate\Routing\Redirector`        |
| `->with('key', 'val')`   | Flash a one-time session message      | `Illuminate\Http\RedirectResponse`     |
| `$request->validate()`   | Validate input, auto-redirect on fail | `Illuminate\Http\Request`              |
| `Rule::unique`           | Unique DB check with ignore support   | `Illuminate\Validation\Rule`           |
| `Password::defaults()`   | Reusable password rule set            | `Illuminate\Validation\Rules\Password` |
