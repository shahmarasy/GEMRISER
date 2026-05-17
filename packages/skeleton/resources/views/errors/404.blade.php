@extends('layouts.app')

@section('content')
<div class="text-center py-20">
    <h1 class="text-6xl font-bold text-gray-300 mb-4">404</h1>
    <p class="text-xl text-gray-600 mb-8">Page not found</p>
    <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Go home</a>
</div>
@endsection
