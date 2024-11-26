<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailAccount;

class UserManagementController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::all();
        
        // Fetch unattached email accounts
        $unattachedEmailAccounts = EmailAccount::where(function($query) {
            $query->whereNull('user_id')
                  ->orWhere('user_id', 0);
        })->get();

        return view('users.index', compact('users', 'unattachedEmailAccounts'));
    }

    // Get information about a specific user's email accounts
    public function show($id)
    {
        $user = User::findOrFail($id);
        $emailAccounts = $user->emailAccounts; // Assuming a relationship exists

        // Fetch unattached email accounts
        $unattachedEmailAccounts = EmailAccount::whereNull('user_id')->get();

        return view('users.show', compact('user', 'emailAccounts', 'unattachedEmailAccounts'));
    }

    // Save a new user
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'surname' => $validatedData['surname'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return redirect()->route('users.show', $user->id)->with('status', 'User created successfully!');
    }

    // Store a new email account
    public function storeEmailAccount(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'email_address' => 'required|email|unique:email_accounts,email_address',
        ]);

        EmailAccount::create([
            'user_id' => $validatedData['user_id'],
            'email_address' => $validatedData['email_address'],
        ]);

        return redirect()->route('users.management')->with('status', 'Email account added successfully!');
    }

    // Remove an email account using the detach method
    public function removeEmailAccount($id)
    {
        // Redirect to the detach method in EmailAccountController
        return app(EmailAccountController::class)->detach($id);
        
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'signature' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'user_id' => 'required|integer', // Ensure user_id is present
        ]);
        // Log validation errors
        if ($validator->fails()) {
            \Log::error('Validation errors:', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Check if the user_id from the form matches the route parameter
        if ($validatedData['user_id'] != $id) {
            return redirect()->back()->withErrors(['user_id' => 'User ID mismatch.']);
        }

        $user = User::findOrFail($id);

        // Update user details
        $user->update([
            'name' => $validatedData['name'],
            'surname' => $validatedData['surname'],
            'signature' => $validatedData['signature'],
        ]);

        // Update password if provided
        if (!empty($validatedData['password'])) {
            $user->update(['password' => bcrypt($validatedData['password'])]);
        }

        return redirect()->route('users.show', $id)->with('status', 'User updated successfully!');
    }

    // Show the form for creating a new user
    public function create()
    {
        return view('users.create');
    }
}
