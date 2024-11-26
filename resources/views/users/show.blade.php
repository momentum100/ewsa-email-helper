@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">User Details</h2>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Name:</label>
        <p id="userName" class="mt-1 text-gray-900">{{ $user->name }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Surname:</label>
        <p id="userSurname" class="mt-1 text-gray-900">{{ $user->surname }}</p>
    </div>
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Email:</label>
        <p id="userEmail" class="mt-1 text-gray-900">{{ $user->email }}</p>
    </div>
    <button onclick="copyToClipboard()" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Copy to Clipboard</button>
</div>

<script>
    function copyToClipboard() {
        const userDetails = `Name: {{ $user->name }}\nSurname: {{ $user->surname }}\nEmail: {{ $user->email }}`;
        navigator.clipboard.writeText(userDetails).then(() => {
            alert('User details copied to clipboard!');
        }, (err) => {
            console.error('Could not copy text: ', err);
        });
    }
</script>
@endsection
