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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            // Plan name (e.g. Free, Pro)
            $table->string('name', 64)->unique()->comment('Plan name, e.g. Free, Pro');

            // Longer description of the plan
            $table->string('description')->nullable()->comment('Detailed plan description');

            // Features as a JSON array, e.g. {"features":["feature1","feature2"]}
            $table->json('features')->nullable()->comment('JSON array of plan features');
            $table->timestamps();
        });
        DB::table('plans')->updateOrInsert(
            ['name' => 'Free'],
            ['description' => 'Ideal for small volume and occassional use.'],
            ['features' => json_encode([
                'data' => [
                    '1 email address',
                    '100 scanned emails a month',
                    'Scans once per hour',
                    'Email analytics dashboard',
                    'Email support',
                ]
            ])]
        );
        DB::table('plans')->updateOrInsert(
            ['name' => 'Pro'],
            ['description' => 'Good for more volume or multiple email addresses.'],
            ['features' => json_encode([
                'data' => [
                    '5 email addresses',
                    '1000 emails per month',
                    'Scans every 5 minutes',
                    'Priority email filtering',
                    'Custom rules & exceptions',
                    'Priority support',
                ]
            ])]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
