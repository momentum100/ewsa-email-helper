<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->unsignedBigInteger('email_account_id')->after('id');
            // If you have a foreign key relationship
            // $table->foreign('email_account_id')->references('id')->on('email_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('email_account_id');
            // If you have a foreign key relationship
            // $table->dropForeign(['email_account_id']);
        });
    }
};
