<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imap_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('host');
            $table->unsignedInteger('port');
            $table->string('encryption')->nullable();
            $table->string('username');
            $table->string('password')->encrypted();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imap_accounts');
    }
};
