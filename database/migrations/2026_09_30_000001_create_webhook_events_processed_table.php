<?php

// Rule 3: Idempotency keys tracking with UUIDs
// Webhook event deduplication table for replay attack and duplicate event prevention

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
        Schema::create('webhook_events_processed', function (Blueprint $table) {
            $table->string('id')->primary(); // Stripe Event ID: evt_...
            $table->string('type');          // Event type, e.g., invoice.payment_succeeded
            $table->timestamp('created_at_stripe')->nullable(); // Stripe event->created timestamp
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->string('id')->primary(); // Rule 3: UUID idempotency key
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('scope')->default('subscription_checkout');
            $table->timestamp('used_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
        Schema::dropIfExists('webhook_events_processed');
    }
};
