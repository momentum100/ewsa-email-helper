<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DownloadEmailsForAccount;
use App\Models\EmailAccount;

class DownloadEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:download-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle()
{
    $accounts = EmailAccount::all();

    foreach ($accounts as $account) {
        \Log::info('Dispatched email download job for account: ' . $account->id);
        
        DownloadEmailsForAccount::dispatch($account);
        
    }

    $this->info('Email download jobs dispatched successfully.');
}


}
