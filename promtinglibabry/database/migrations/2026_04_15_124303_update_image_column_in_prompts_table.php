<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->json('images')->nullable()->after('prompt_text');
        });

        // Migrate existing data
        DB::table('prompts')->orderBy('id')->chunk(100, function ($prompts) {
            foreach ($prompts as $prompt) {
                if (!empty($prompt->image_url)) {
                    DB::table('prompts')->where('id', $prompt->id)->update([
                        'images' => json_encode([$prompt->image_url])
                    ]);
                }
            }
        });

        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('prompts', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('images');
        });

        // Try to recover data backwards
        DB::table('prompts')->orderBy('id')->chunk(100, function ($prompts) {
            foreach ($prompts as $prompt) {
                if (!empty($prompt->images)) {
                    $images = json_decode($prompt->images, true);
                    if (is_array($images) && count($images) > 0) {
                        DB::table('prompts')->where('id', $prompt->id)->update([
                            'image_url' => $images[0]
                        ]);
                    }
                }
            }
        });

        Schema::table('prompts', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};
