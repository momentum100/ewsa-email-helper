<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailAccount;

class EmailAccountController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'email_address' => 'required|email',
            'imap_user' => 'required|string',
            'imap_pass' => 'required|string',
            'imap_host' => 'required|string',
            'imap_port' => 'required|integer',
            'imap_encryption' => 'nullable|string',
            'smtp_user' => 'required|string',
            'smtp_pass' => 'required|string',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_encryption' => 'nullable|string',
        ]);

        $emailAccount = new EmailAccount($validatedData);
        //$emailAccount->user_id = auth()->id(); // Assuming the user is authenticated 
        $emailAccount->user_id = null;
        $emailAccount->save();

        // Fetch email accounts that are not attached to any user
        $unattachedEmailAccounts = EmailAccount::whereNull('user_id')->get();

        // Pass these email accounts to the view
        return view('email_accounts.create', compact('unattachedEmailAccounts'));
    }

    public function index()
    {
        $emailAccounts = EmailAccount::all();
        return view('email_accounts.index', compact('emailAccounts'));
    }

    public function detach(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $emailAccount = EmailAccount::where('user_id', $validatedData['user_id'])->findOrFail($id);
        $emailAccount->user_id = null; // Void the user_id
        $emailAccount->save();

        return redirect()->route('users.index')->with('status', 'Email account detached successfully!');
    }

    public function attach(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'email_account_id' => 'required|exists:email_accounts,id',
        ]);

        $emailAccount = EmailAccount::findOrFail($validatedData['email_account_id']);
        $emailAccount->user_id = $validatedData['user_id'];
        $emailAccount->save();

        return redirect()->route('users.index')->with('status', 'Email account attached successfully!');
    }
}