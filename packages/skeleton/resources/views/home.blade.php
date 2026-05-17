@extends('layouts.app')

@section('content')
<div class="text-center py-20">
    <h1 class="text-4xl font-bold text-gray-800 mb-4">Welcome to Gemriser</h1>
    <p class="text-xl text-gray-600 mb-8">Modern micro PHP framework — spiritual successor to Lumen</p>
    <div class="space-x-4">
        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Get Started</a>
        <a href="{{ route('login') }}" class="bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300">Login</a>
    </div>
</div>
@endsection
