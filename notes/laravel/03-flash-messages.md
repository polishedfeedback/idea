# Laravel — Flash Messages & Alpine.js

---

## Flash Messages (Session)

A flash message is a value stored in the session for **exactly one request**. After it's read once, it's gone. Laravel uses this for one-time notifications like "Login successful" or "Post deleted".

- Stored in the session via `->with('key', 'value')` on a redirect
- Read in Blade via `session('key')`
- Lives in `Illuminate\Http\RedirectResponse`

```php
// In your controller — set the flash message on redirect
return redirect('/dashboard')->with('success', 'You are now logged in!');
return redirect()->back()->with('error', 'Something went wrong.');
return redirect('/posts')->with('info', 'Post saved as draft.');
```

Reading it in Blade:

```blade
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

The key name (`success`, `error`, `info`) is entirely your choice — Laravel doesn't enforce any specific names. Just be consistent across your app.

---

## Showing flash messages globally

Rather than checking for flash messages on every single page, put it once in your layout file so every page inherits it automatically.

```blade
{{-- resources/views/layouts/app.blade.php --}}
<body>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @yield('content')
</body>
```

Now every view that extends this layout will automatically show flash messages without any extra code.

---

## Alpine.js

Alpine.js is a lightweight JavaScript library that lets you add interactivity (show/hide, transitions, timers) directly in your HTML using attributes. No separate JS files needed.

- Think of it as Blade's frontend companion — Blade handles server-side, Alpine handles client-side
- Added via a `<script>` tag, no build step required
- Uses `x-` prefixed attributes directly on HTML elements

```html
<script src="//unpkg.com/alpinejs" defer></script>
```

---

## Auto-dismissing flash messages with Alpine

The most common use of Alpine in Laravel apps is making flash messages disappear automatically after a few seconds.

```blade
@if(session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3000)"
        x-transition
    >
        {{ session('success') }}
    </div>
@endif
```

What each Alpine attribute does:

- `x-data="{ show: true }"` — defines a local state variable `show`, starts as `true`
- `x-show="show"` — shows or hides the element based on the value of `show`
- `x-init="setTimeout(() => show = false, 3000)"` — runs on page load, sets `show` to `false` after 3 seconds
- `x-transition` — adds a smooth fade in/out animation when the element appears or disappears

---

## Alpine core attributes

| Attribute                 | What it does                                |
|---------------------------|---------------------------------------------|
| `x-data`                  | Defines local state for a component         |
| `x-show`                  | Shows/hides an element based on a condition |
| `x-init`                  | Runs JS when the component initialises      |
| `x-on:click` / `@click`   | Listens for a click event                   |
| `x-bind:class` / `:class` | Dynamically binds a class                   |
| `x-transition`            | Adds CSS transitions on show/hide           |
| `x-text`                  | Sets the text content of an element         |

```blade
{{-- Toggle a menu open/closed --}}
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle Menu</button>

    <nav x-show="open" x-transition>
        <a href="/">Home</a>
        <a href="/about">About</a>
    </nav>
</div>
```

---

## Putting it all together — dismissible flash message

```blade
{{-- resources/views/layouts/app.blade.php --}}

@if(session('success'))
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 4000)"
        x-transition.opacity
        class="alert alert-success"
    >
        <span>{{ session('success') }}</span>
        <button @click="show = false">&times;</button>
    </div>
@endif
```

This gives you:
- A flash message set from the controller via `->with('success', '...')`
- Rendered in Blade via `session('success')`
- Auto-dismissed after 4 seconds via Alpine's `x-init` + `setTimeout`
- Manually dismissible via the `&times;` button
- Smooth fade via `x-transition.opacity`

---

## Quick reference

| Concept                  | What it does                                   | Where it lives                       |
|--------------------------|------------------------------------------------|--------------------------------------|
| `->with('key', 'value')` | Flash a value into the session for one request | `Illuminate\Http\RedirectResponse`   |
| `session('key')`         | Read a flash value in Blade                    | Blade / global `session()` helper    |
| `x-data`                 | Define local Alpine state                      | Alpine.js attribute                  |
| `x-show`                 | Show/hide based on state                       | Alpine.js attribute                  |
| `x-init`                 | Run JS on component load                       | Alpine.js attribute                  |
| `x-transition`           | Animate show/hide                              | Alpine.js attribute                  |
| `@click`                 | Handle click events                            | Alpine.js shorthand for `x-on:click` |
