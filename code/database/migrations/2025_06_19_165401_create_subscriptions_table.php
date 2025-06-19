<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
A subscription is a join between a user and a plan
*/
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            // plan_id: unsigned FK to plans
            $table->unsignedBigInteger('plan_id')->nullable()->default(null)
                ->comment('Foreign key to plans table');
            // user_id: unsigned FK to users
            $table->unsignedBigInteger('user_id')->nullable()->default(null)
                ->comment('Foreign key to users table');
            $table->decimal('price')->nullable()->default(null);
            // start_at: datetime when started (nullable)
            $table->dateTime('start_at')->nullable()->comment('Datetime when subscription started');

            // last_charged_at: datetime last charged (nullable)
            $table->dateTime('last_charged_at')->nullable()->comment('Datetime when subscription was last charged');

            // paused_at: datetime subscription paused (nullable)
            $table->dateTime('paused_at')->nullable()->comment('Datetime when subscription was paused');

            // end_at: datetime subscription ended (nullable)
            $table->dateTime('end_at')->nullable()->comment('Datetime when subscription ended');

            // status: int - active(currently billable), paused(user temporarily paused), canceled(user canceled), failed(payment could not be processed)
            $table->unsignedTinyInteger('status')
                ->default(0)
                ->comment('0=active, 1=paused, 2=canceled, 3=failed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
