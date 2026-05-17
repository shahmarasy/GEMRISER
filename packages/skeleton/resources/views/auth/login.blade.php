@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-6">Login</h1>

    @if(isset($error))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ $error }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        {{ csrf_field() }}
        <div>
            <label class="block text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Login</button>
    </form>
    <p class="mt-4 text-center text-gray-600">
        Don't have an account? <a href="{{ route('register') }}" class="text-blue-600">Register</a>
    </p>
</div>
@endsection
