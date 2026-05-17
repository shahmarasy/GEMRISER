@extends('layouts.app')

@section('content')
<div class="py-8">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600">Welcome back, <strong>{{ $user->name }}</strong>!</p>
        <p class="text-gray-500 text-sm mt-2">Email: {{ $user->email }}</p>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-semibold mb-4">Registered Users</h2>
        <table class="w-full bg-white rounded-lg shadow">
            <thead>
                <tr class="border-b">
                    <th class="text-left px-4 py-2">ID</th>
                    <th class="text-left px-4 py-2">Name</th>
                    <th class="text-left px-4 py-2">Email</th>
                    <th class="text-left px-4 py-2">Joined</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::all() as $user)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $user->id }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
