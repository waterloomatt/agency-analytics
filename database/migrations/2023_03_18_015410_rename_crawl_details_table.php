<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!app()->runningUnitTests()) {
            Schema::table('crawl_details', function (Blueprint $table) {
                $table->dropForeign(['crawl_id']);


                $table
                    ->foreign('crawl_id', 'crawl_pages_crawl_id_foreign')
                    ->references('id')
                    ->on('crawls');
            });
        }

        Schema::rename('crawl_details', 'crawl_pages');
    }

    public function down(): void
    {
        Schema::rename('crawl_pages', 'crawl_details');

        if (!app()->runningUnitTests()) {
            Schema::table('crawl_details', function (Blueprint $table) {
                $table
                    ->foreign('crawl_id', 'crawl_details_crawl_id_foreign')
                    ->references('id')
                    ->on('crawls');
            });
        }
    }
};
