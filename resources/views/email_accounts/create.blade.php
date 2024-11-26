@extends('layouts.app')

@section('content')
<div class="container mx-auto flex justify-center">
    <div class="w-full max-w-md">
        <h1 class="text-center text-2xl font-bold mb-6">Add Email Account</h1>

        <form action="{{ route('email_accounts.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="form-group">
                <label for="email_address" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" name="email_address" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="imap_user" class="block text-sm font-medium text-gray-700">IMAP User</label>
                <input type="text" name="imap_user" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="imap_pass" class="block text-sm font-medium text-gray-700">IMAP Password</label>
                <input type="password" name="imap_pass" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="imap_host" class="block text-sm font-medium text-gray-700">IMAP Host</label>
                <input type="text" name="imap_host" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="imap_port" class="block text-sm font-medium text-gray-700">IMAP Port</label>
                <input type="number" name="imap_port" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="imap_encryption" class="block text-sm font-medium text-gray-700">IMAP Encryption</label>
                <input type="text" name="imap_encryption" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="form-group">
                <label for="smtp_user" class="block text-sm font-medium text-gray-700">SMTP User</label>
                <input type="text" name="smtp_user" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="smtp_pass" class="block text-sm font-medium text-gray-700">SMTP Password</label>
                <input type="password" name="smtp_pass" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="smtp_host" class="block text-sm font-medium text-gray-700">SMTP Host</label>
                <input type="text" name="smtp_host" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="smtp_port" class="block text-sm font-medium text-gray-700">SMTP Port</label>
                <input type="number" name="smtp_port" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="form-group">
                <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">SMTP Encryption</label>
                <input type="text" name="smtp_encryption" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="flex justify-between mt-6">
                <button type="submit" class="btn btn-primary bg-indigo-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-indigo-700">Add Email Account</button>
                <button type="button" class="btn btn-secondary bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-gray-700">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection
