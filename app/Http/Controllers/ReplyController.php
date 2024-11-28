<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;
use Illuminate\Support\Facades\Log;
use App\Models\Email;
use App\Models\EmailAccount;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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

        // Get the email account
        $emailAccount = EmailAccount::findOrFail($request->email_account_id);

        try {
            // Create email service instance
            $emailService = new EmailService($emailAccount);

            // Send email using the service
            $emailService->sendEmail(
                $request->reply_to,
                $request->reply_subject,
                $request->reply_body,
                $request->reply_to_cc
            );

            // Update the status to sent
            $reply->status = 1;
            $reply->save();

            return redirect()->route('replies.show', $id)
                            ->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to send email: " . $e->getMessage());
            return redirect()->route('replies.show', $id)
                            ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
