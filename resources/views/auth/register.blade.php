<x-layout>
   <x-form title="Register an account" description="Start tracking your ideas today">
    <form action="/register" method="POST" class="mt-10 space-y-4">
        @csrf
        <x-form.input name="name" label="Name"/>
        <x-form.input name="email" label="Email" type="email"/>
        <x-form.input name="password" label="Password" type="password"/>
        <button type="submit" class="btn w-full mt-2 h-10">Create account</button>
    </form>
       </x-form>
</x-layout>
