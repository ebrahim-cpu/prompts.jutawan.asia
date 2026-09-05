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
        if (!Schema::hasTable('user_access_logs')) {
            Schema::create('user_access_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('user_name', 255)->nullable();
                $table->string('user_email', 255)->nullable();
                $table->string('event_type', 50)->default('LOGIN');
                $table->string('ip_address', 45);
                $table->string('user_agent', 500)->nullable();
                $table->string('url', 500)->nullable();
                $table->string('method', 10)->nullable()->default('POST');
                $table->string('referer', 500)->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->index(['user_id', 'event_type']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_access_logs');
    }
};
