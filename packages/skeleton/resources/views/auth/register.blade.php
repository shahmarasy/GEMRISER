@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Register</h1>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        {{ csrf_field() }}
        <div>
            <label class="block text-gray-700 mb-1">Name</label>
            <input type="text" name="name" required minlength="3" class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required minlength="8" class="w-full border rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Register</button>
    </form>
    <p class="mt-4 text-center text-gray-600">
        Already have an account? <a href="{{ route('login') }}" class="text-blue-600">Login</a>
    </p>
</div>
@endsection
