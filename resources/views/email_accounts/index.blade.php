@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Email Accounts</h1>
    <a href="{{ route('email_accounts.create') }}" class="inline-block mb-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Create New Email Account</a>
    <table class="table-auto w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-200 px-4 py-2">User ID</th>
                <th class="border border-gray-200 px-4 py-2">Email Address</th>
                <th class="border border-gray-200 px-4 py-2">IMAP Host</th>
                <th class="border border-gray-200 px-4 py-2">IMAP Port</th>
                <th class="border border-gray-200 px-4 py-2">SMTP Host</th>
                <th class="border border-gray-200 px-4 py-2">SMTP Port</th>
                <th class="border border-gray-200 px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emailAccounts as $account)
            <tr>
                <td class="border border-gray-200 px-4 py-2">{{ $account->user_id }}</td>
                <td class="border border-gray-200 px-4 py-2">{{ $account->email_address }}</td>
                <td class="border border-gray-200 px-4 py-2">{{ $account->imap_host }}</td>
                <td class="border border-gray-200 px-4 py-2">{{ $account->imap_port }}</td>
                <td class="border border-gray-200 px-4 py-2">{{ $account->smtp_host }}</td>
                <td class="border border-gray-200 px-4 py-2">{{ $account->smtp_port }}</td>
                <td class="border border-gray-200 px-4 py-2">
                    <!-- Add any actions like edit or delete here -->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
