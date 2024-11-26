<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;
use Illuminate\Support\Facades\Log;
use App\Models\Email;
use App\Models\EmailAccount;

class ReplyController extends Controller
{
    public function show($id)
    {
        $reply = Reply::findOrFail($id);

        // Extract email, AI reply, and status data from the Reply model
        $email = (object) [
            'id' => $reply->id,
            'from' => $reply->from,
            'to' => $reply->to,
            'subject' => $reply->origin_subject,
            'body' => $reply->origin_body,
        ];

        $aiReply = (object) [
            'subject' => $reply->reply_subject,
            'body' => $reply->reply_body,
        ];

        $status = $reply->status;

        // Fetch email accounts connected to the authenticated user
        $userEmailAccounts = EmailAccount::where('user_id', auth()->id())->get();

        return view('replies.show', compact('email', 'aiReply', 'status', 'userEmailAccounts'));
    }

    public function saveReply($to, $from, $originSubject, $originBody, $replySubject, $replyBody, $emailId)
    {
        // Create a new reply
        $reply = Reply::create([
            'to' => $to,
            'from' => $from,
            'origin_subject' => $originSubject,
            'origin_body' => $originBody,
            'reply_subject' => $replySubject,
            'reply_body' => $replyBody,
            'parent_id' => $emailId, // Save the email ID as parent_id
        ]);

        // Update the email with the reply ID
        $email = Email::find($emailId);
        $email->reply_id = $reply->id;
        $email->save();

        Log::info("Reply saved with ID: {$reply->id}");

        return $reply;
    }

    public function sendAIReply(Request $request, $id)
    {
        // Retrieve the reply using the ID
        $reply = Reply::findOrFail($id);

        // Fetch email accounts connected to the authenticated user
        $userEmailAccounts = EmailAccount::where('user_id', auth()->id())->get();

        // Logic to send the AI reply
        // For example, you might send an email or update the status of the reply

        // Update the status to 1
        $reply->status = 1;
        $reply->save();

        // Redirect back with a success message and user email accounts
        return redirect()->route('replies.show', $id)
                         ->with('success', 'AI reply sent successfully.')
                         ->with('userEmailAccounts', $userEmailAccounts);
    }
}
