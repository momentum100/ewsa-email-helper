@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Email and AI Reply</h1>

    @if($status == 1)
        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded">
            <div class="text-5xl font-bold text-red-600">This reply was already sent.</div>
        </div>
    @endif

    <div class="flex space-x-4">
        <!-- Original Email Column -->
        <div class="w-1/2 border p-4">
            <h2 class="text-xl font-semibold mb-2">Original Email</h2>
            <div class="mb-2">
                <label class="font-bold">From:</label> {{ $email->from }}
            </div>
            <div class="mb-2">
                <label class="font-bold">To:</label> {{ $email->to }}
            </div>
            <div class="mb-2">
                <label class="font-bold">Subject:</label> {{ $email->subject }}
            </div>
            <div class="mb-2">
                <label class="font-bold">Body:</label>
                <div class="bg-gray-100 p-2 rounded">
                    {!! nl2br(e($email->body)) !!}
                </div>
            </div>
        </div>

        <!-- AI Reply Column -->
        <div class="w-1/2 border p-4">
            <h2 class="text-xl font-semibold mb-2">AI Reply</h2>
            <form method="POST" action="{{ route('send.ai.reply', $email->id) }}">
                @csrf
                <div class="mb-2">
                    <label class="font-bold">Send From:</label>
                    <select name="send_from" class="border p-2 rounded w-full" {{ $status == 1 ? 'disabled' : '' }}>
                        @foreach($userEmailAccounts as $account)
                            <option value="{{ $account->email_address }}">{{ $account->email_address }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="font-bold">Subject:</label>
                    <input type="text" name="reply_subject" value="{{ $aiReply->subject }}" class="border p-2 rounded w-full" {{ $status == 1 ? 'disabled' : '' }}>
                </div>
                <div class="mb-4">
                    <label class="font-bold">Body:</label>
                    <textarea id="reply_body" name="reply_body" class="border p-2 rounded w-full" rows="10" {{ $status == 1 ? 'disabled' : '' }}>{{ $aiReply->body }}</textarea>
                </div>
                <div class="mt-4 flex justify-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold text-2xl py-4 px-16 rounded {{ $status == 1 ? 'bg-gray-300 text-gray-700 cursor-not-allowed' : '' }}" {{ $status == 1 ? 'disabled' : '' }}>Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('reply_body');
        
        function autoResize() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        }

        textarea.addEventListener('input', autoResize);
        
        // Initial resize to fit content
        autoResize.call(textarea);
    });
</script>
@endsection
