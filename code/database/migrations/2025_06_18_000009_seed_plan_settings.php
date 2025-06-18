<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'plans'],
            ['value' => json_encode([
                'free' => [
                    'max_tokens' => 1,
                    'monthly_limit' => 100,
                ],
                'pro' => [
                    'max_tokens' => 5,
                    'monthly_limit' => 1000,
                ],
            ])]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'plans')->delete();
    }
};
