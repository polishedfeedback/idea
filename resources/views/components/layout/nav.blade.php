<nav class="border-b border-border px-6">
    <div class="max-w-7xl mx-auto h-15 flex items-center justify-between">
        <div>
            <a href="/" >
                <img alt="logo" src="/images/logo.png" width="75"/>
            </a>
        </div>
        @guest
            <div class="flex gap-x-5 items-center">
                <a href="/login">Login</a>
                <a href="/register" class="btn">Register</a>
            </div>
        @endguest
        @auth
{{--            <div class="flex gap-x-5 items-center">--}}
{{--                <a href="/logout" class="btn">Logout</a>--}}
{{--            </div>--}}
            <form action="/logout" method="POST">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @endauth
    </div>
</nav>
