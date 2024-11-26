@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Users Management</h1>

    @if (session('status'))
        <div class="alert alert-success bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <a href="{{ route('users.create') }}" class="inline-block mb-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New User</a>

    <table class="table-auto w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-200 px-4 py-2">Name</th>
                <th class="border border-gray-200 px-4 py-2">Surname</th>
                <th class="border border-gray-200 px-4 py-2">Email</th>
                <th class="border border-gray-200 px-4 py-2">Signature</th>
                <th class="border border-gray-200 px-4 py-2">Actions</th>
                <th class="border border-gray-200 px-4 py-2">Email Accounts</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr>
                <form action="{{ route('users.update', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <td class="border border-gray-200 px-4 py-2">
                        <input type="text" name="name" value="{{ $user->name }}" class="border border-gray-300 p-1">
                    </td>
                    <td class="border border-gray-200 px-4 py-2">
                        <input type="text" name="surname" value="{{ $user->surname }}" class="border border-gray-300 p-1">
                    </td>
                    <td class="border border-gray-200 px-4 py-2">
                        <input type="email" name="email" value="{{ $user->email }}" class="border border-gray-300 p-1">
                    </td>
                    <td class="border border-gray-200 px-4 py-2">
                        <input type="text" name="signature" value="{{ $user->signature }}" class="border border-gray-300 p-1">
                    </td>
                    <td class="border border-gray-200 px-4 py-2">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded">Update</button>
                    </td>
                </form>
                <td class="border border-gray-200 px-4 py-2">
                    @foreach($user->emailAccounts as $emailAccount)
                        <div>
                            {{ $emailAccount->email_address }}  {{ $emailAccount->id }}
                            <form action="{{ route('emailAccounts.detach', ['id' => $emailAccount->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <button type="submit" class="text-red-500 hover:underline">Remove</button>
                            </form>
                        </div>
                    @endforeach
                    <form action="{{ route('emailAccounts.attach') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <select name="email_account_id" class="border border-gray-300 p-1">
                            @foreach($unattachedEmailAccounts as $emailAccount)
                                <option value="{{ $emailAccount->id }}">{{ $emailAccount->email_address }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">Attach</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="border border-gray-200 px-4 py-2 text-center">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
