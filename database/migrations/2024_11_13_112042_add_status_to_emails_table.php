<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToEmailsTable extends Migration
{
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->enum('status', ['new', 'processed', 'failed'])->default('new');
        });
    }

    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}