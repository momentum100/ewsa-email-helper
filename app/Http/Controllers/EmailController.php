<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Email;
use App\Models\Reply;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\ReplyController;
use App\Services\CostManager;

class EmailController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            $emails = Email::orderBy('received_at', 'desc')->get();
        } else {
            $emails = Email::whereHas('emailAccount', function ($query) {
                $query->where('user_id', auth()->id());
            })->orderBy('received_at', 'desc')->get();
        }

        // Prepare a status message if no emails are found
        $status = $emails->isEmpty() ? 'No emails found.' : null;

        return view('emails.index', compact('emails', 'status'));
    }

    public function categorize(Request $request)
    {
        // Fetch selected email IDs from the request
        $selectedEmailIds = $request->input('selected_emails', []);

        // If no emails are selected, process all
        $emails = empty($selectedEmailIds) ? Email::all() : Email::whereIn('id', $selectedEmailIds)->get();

        // Log the start of the categorization process
        Log::info('Starting email categorization process.');

        // Instantiate the TelegramController
        $telegramController = new TelegramController();

        // Instantiate the OpenAIController
        $openAIController = new OpenAIController();

        // Instantiate the ReplyController
        $replyController = new ReplyController();

        $costManager = new CostManager();

        // Categorize each email using OpenAI
        foreach ($emails as $email) {

            $totalCost = 0;
            // Strip HTML tags from the email body
            $cleanBody = strip_tags($email->body);

            // Combine subject and body for categorization
            $contentForCategorization = "Subject: {$email->subject}\n\nBody: {$cleanBody}";

            // Log the email being processed
            Log::info("Processing email ID: {$email->id}");

            // Check if the email is worth analyzing
            $worthAnalyzingResult = $openAIController->isWorthAnalyzing($contentForCategorization);
            $isPriceRequest = $worthAnalyzingResult['isPriceRequest'] ?? false;
            $cost = number_format((float)$worthAnalyzingResult['cost'], 4);
            if (!$isPriceRequest) {
                Log::info("Email ID: {$email->id} is not a price request. Skipping categorization. Cost: " . $costManager->formatCost($worthAnalyzingResult['cost']));
                // Set email category to General
                $email->category = 'General';
                $email->save();
                // Send a Telegram message indicating the email was skipped
                $subject = "Skipped Email: {$email->subject} \n API Cost: {$cost}";
                $body = "The email with subject '{$email->subject}' was skipped as it is not a price request.";
                $telegramController->sendMessage($subject, $body);
                continue;
            }

            // Proceed with categorization if it's worth analyzing
            $categorizationResult = $openAIController->categorizeEmail($contentForCategorization);
            $result = $categorizationResult['response'];
            $cost = $categorizationResult['cost'];

            // Log the result of the categorization
            Log::info('Categorization result:', ['result' => $result]);

            // Log the API response and cost
            Log::info("OpenAI API response for email ID: {$email->id}", ['response' => $result]);
            Log::info("OpenAI API cost for email ID: {$email->id}", ['cost' => $cost]);

            // Assuming the API returns a category in the response
            $responseContent = json_decode($result->choices[0]->message->content, true);
            $category = $responseContent['category'];
            $email->category = $category;

            // Save the updated category
            $email->save();

            // Log the result of the categorization
            Log::info("Email ID: {$email->id} categorized as: {$category}");

            $costManager->addCost($categorizationResult['cost']);

            // New logic to handle different scenarios
            if ($responseContent['contains_price_request']) {
                $subject = "RE: {$email->subject}";
                $body = "";

                if (!$responseContent['data_complete']) { // DATA INCOMPLETE
                    // Generate email asking for more details
                    $providedData = json_encode($responseContent['provided_data'], JSON_PRETTY_PRINT);
                    $language = $responseContent['language'];

                    // Use the new method to generate the email and get the cost
                    $emailGenerationResult = $openAIController->generateMissingDataEmail($language, $providedData, "", $contentForCategorization);
                    $emailResponse = $emailGenerationResult['response'];
                    $emailCost = $emailGenerationResult['cost'];

                    $costManager->addCost($emailCost);

                    // Log the generated email and cost
                    Log::info("Generated email for missing data request for email ID: {$email->id}", ['response' => $emailResponse]);
                    Log::info("OpenAI API cost for generating email for email ID: {$email->id}", ['cost' => $emailCost]);

                    // Set the body for the incomplete data scenario
                    $body = $emailResponse['choices'][0]['message']['content'];

                    Log::info("Email ID: {$email->id} is incomplete. Sending request for more details.");
                } else { // DATA COMPLETE
                    // Set the body for the complete data scenario
                    $body = "Email from: {$email->from}\n\nBody seems complete. Thank you for providing all the necessary details.";

                    $body .= "\n\nTotal API Cost for this email: $" . $costManager->formatCost($costManager->getTotalCost());

                    Log::info("Email ID: {$email->id} is complete. Sending thank you message.");

                    // Set email category to PRICE_REQUEST_COMPLETE
                    $email->category = 'PRICE_REQUEST_COMPLETE';
                    $email->save();
                }

                // Append user signature to the body
                $userSignature = auth()->user()->signature;
                $body .= "\n\n" . $userSignature;

                // Send message using TelegramController
                $response = $telegramController->sendMessage($subject, $body);

                // Log the response
                Log::info("Sent message to Telegram group for email ID: {$email->id}", ['response' => $response]);

                // Use the ReplyController to save the reply
                $replyController->saveReply(
                    $email->to,
                    $email->from,
                    $email->subject,
                    $email->body, // AI generated content
                    $subject, // AI generated content
                    $body,
                    $email->id
                );
            }
        }

        // Log the completion of the categorization process
        Log::info('Email categorization process completed.');

        // Redirect back with a success message
        return redirect()->route('emails.index')->with('status', 'Emails categorized successfully!');
    }
}

