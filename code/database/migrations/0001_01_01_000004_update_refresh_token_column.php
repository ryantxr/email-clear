<?php

use App\Models\UserToken;
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
        if (Schema::hasTable('user_tokens') && Schema::hasColumn('user_tokens', 'refresh_token')) {
            Schema::table('user_tokens', function (Blueprint $table) {
                $table->text('refresh_token')->change();
            });

            UserToken::query()->each(function (UserToken $token) {
                $token->save();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_tokens') && Schema::hasColumn('user_tokens', 'refresh_token')) {
            Schema::table('user_tokens', function (Blueprint $table) {
                $table->string('refresh_token')->change();
            });
        }
    }
};
