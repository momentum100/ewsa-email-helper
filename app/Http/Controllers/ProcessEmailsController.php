<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ProcessEmailsController extends Controller
{
    public function process()
    {
        // Trigger the email download command
        Artisan::call('app:download-emails');

        return redirect()->route('emails.index')->with('status', 'Emails downloaded successfully.');
    }
}


