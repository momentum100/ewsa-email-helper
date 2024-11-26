<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email_address');
            $table->string('imap_user');
            $table->string('imap_pass');
            $table->string('imap_host');
            $table->integer('imap_port');
            $table->string('imap_encryption')->nullable();
            $table->string('smtp_user');
            $table->string('smtp_pass');
            $table->string('smtp_host');
            $table->integer('smtp_port');
            $table->string('smtp_encryption')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_accounts');
    }
}