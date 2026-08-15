<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->default(3)->after('category'); // 1-5 stars
            $table->string('tags')->nullable()->after('rating'); // comma-separated tags
        });
    }

    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn(['rating', 'tags']);
        });
    }
};
