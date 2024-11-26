<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::create('emails', function (Blueprint $table) {
        $table->id();
        $table->string('from');
        $table->string('to');
        $table->string('subject');
        $table->text('body');
        $table->string('category', 255)->nullable(); 
        $table->timestamp('received_at')->nullable();
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
