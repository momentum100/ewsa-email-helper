@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Emails</h1>

    @if (session('status'))
        <div class="alert alert-success bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <form id="emailForm" method="POST" action="{{ route('categorize.emails') }}">
        @csrf
        <a href="{{ route('process.emails') }}" class="inline-block mb-3 bg-blue-500 hover:bg-blue-700 text-blue font-bold py-2 px-4 rounded">Receive Emails</a>
        <button type="submit" class="inline-block mb-3 bg-purple-500 hover:bg-purple-700 text-blue font-bold py-2 px-4 rounded">AI Process Emails</button>
        
        @if(auth()->check() && auth()->user()->is_admin)
            <a href="{{ route('users.index') }}" class="inline-block mb-3 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Users Management</a>
            <a href="{{ route('email_accounts.index') }}" class="inline-block mb-3 bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">Email Accounts</a>
        @endif

        <table class="table-auto w-full border-collapse border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-200 px-4 py-2">Select</th>
                    <th class="border border-gray-200 px-4 py-2">From</th>
                    <th class="border border-gray-200 px-4 py-2">To</th>
                    <th class="border border-gray-200 px-4 py-2">Subject</th>
                    <th class="border border-gray-200 px-4 py-2">Received At</th>
                    <th class="border border-gray-200 px-4 py-2">Category</th>
                    <th class="border border-gray-200 px-4 py-2">Reply ID</th>
                    <th class="border border-gray-200 px-4 py-2">Email Account ID</th>
                </tr>
            </thead>
            <tbody>
                @forelse($emails as $email)
                <tr class="hover:bg-gray-50 cursor-pointer" onclick="toggleEmailContent({{ $email->id }})">
                    <td class="border border-gray-200 px-4 py-2">
                        <input type="checkbox" name="selected_emails[]" value="{{ $email->id }}" onclick="event.stopPropagation();">
                    </td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->from }}</td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->to }}</td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->subject }}</td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->received_at }}</td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->category }}</td>
                    <td class="border border-gray-200 px-4 py-2">
                        @if($email->reply_id)
                            <a href="{{ route('replies.show', ['id' => $email->reply_id]) }}" class="text-blue-500 hover:underline" onclick="event.stopPropagation();">
                                reply #{{ $email->reply_id }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="border border-gray-200 px-4 py-2">{{ $email->email_account_id }}</td>
                </tr>
                <tr id="email-content-{{ $email->id }}" class="hidden transition-all duration-300 ease-in-out">
                    <td colspan="6" class="border border-gray-200 px-4 py-2">
                        <div class="email-content bg-gray-50 p-4">
                            {!! nl2br(e($email->body)) !!}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="border border-gray-200 px-4 py-2 text-center">No emails found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </form>
</div>

<script>
    function toggleEmailContent(emailId) {
        const contentRow = document.getElementById(`email-content-${emailId}`);
        contentRow.classList.toggle('hidden');
    }
</script>
@endsection
