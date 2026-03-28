<x-layout>
   <x-form title="Login" description="Great to see you back">
    <form action="/login" method="POST" class="mt-10 space-y-4">
        @csrf
        <x-form.input name="email" label="Email" type="email"/>
        <x-form.input name="password" label="Password" type="password"/>
        <button type="submit" class="btn w-full mt-2 h-10">Login to your account</button>
    </form>
       </x-form>
</x-layout>
