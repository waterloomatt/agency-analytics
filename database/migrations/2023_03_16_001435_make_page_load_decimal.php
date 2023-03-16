<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crawl_details', function (Blueprint $table) {
            $table->decimal('page_load', 8, 6)->change();
        });

        Schema::table('crawls', function (Blueprint $table) {
            $table->decimal('avg_page_load', 8, 6)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('crawl_details', function (Blueprint $table) {
            $table->integer('page_load')->change();
        });

        Schema::table('crawls', function (Blueprint $table) {
            $table->decimal('avg_page_load')->default(0)->change();
        });
    }
};
