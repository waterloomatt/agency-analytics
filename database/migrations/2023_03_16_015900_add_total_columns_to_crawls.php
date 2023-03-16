<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('crawls', function (Blueprint $table) {
            $table->integer('pages')->default(0)->after('status');
            $table->integer('unique_images')->default(0)->after('pages');
            $table->integer('unique_internal_links')->default(0)->after('unique_images');
            $table->integer('unique_external_links')->default(0)->after('unique_internal_links');
        });
    }

    public function down(): void
    {
        Schema::table('crawls', function (Blueprint $table) {
            $table->dropColumn([
                'pages',
                'unique_images',
                'unique_internal_links',
                'unique_external_links',
            ]);
        });
    }
};
