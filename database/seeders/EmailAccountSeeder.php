<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailAccount;

class EmailAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emailAccounts = [
            [
                'user_id' => 0, // Adjust user_id as necessary
                'email_address' => 'courtney.wright@wixidoi.com',
                'imap_user' => 'courtney.wright@wixidoi.com',
                'imap_pass' => 'bjgydRoC',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'courtney.wright@wixidoi.com',
                'smtp_pass' => 'bjgydRoC',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'philip.melendez@allcomprehensive.com',
                'imap_user' => 'philip.melendez@allcomprehensive.com',
                'imap_pass' => 'Qc7ILxqL',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'philip.melendez@allcomprehensive.com',
                'smtp_pass' => 'Qc7ILxqL',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'david.walker@webisoo.com',
                'imap_user' => 'david.walker@webisoo.com',
                'imap_pass' => '94wqRqsQ',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'david.walker@webisoo.com',
                'smtp_pass' => '94wqRqsQ',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'peter.fowler@yooriko.com',
                'imap_user' => 'peter.fowler@yooriko.com',
                'imap_pass' => 'ks5R7ETt',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'peter.fowler@yooriko.com',
                'smtp_pass' => 'ks5R7ETt',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'michael.hensley@wixidoi.com',
                'imap_user' => 'michael.hensley@wixidoi.com',
                'imap_pass' => 'iFj8fMcY',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'michael.hensley@wixidoi.com',
                'smtp_pass' => 'iFj8fMcY',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'sara.lewis@looiko.com',
                'imap_user' => 'sara.lewis@looiko.com',
                'imap_pass' => 'uWpZjCCy',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'sara.lewis@looiko.com',
                'smtp_pass' => 'uWpZjCCy',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
            [
                'user_id' => 0,
                'email_address' => 'leon.reid@workiko.com',
                'imap_user' => 'leon.reid@workiko.com',
                'imap_pass' => 'XwfkR7AY',
                'imap_host' => 'imap.yandex.ru',
                'imap_port' => 993,
                'smtp_user' => 'leon.reid@workiko.com',
                'smtp_pass' => 'XwfkR7AY',
                'smtp_host' => 'smtp.yandex.ru',
                'smtp_port' => 465,
            ],
        ];

        foreach ($emailAccounts as $account) {
            EmailAccount::create($account);
        }
    }
}
