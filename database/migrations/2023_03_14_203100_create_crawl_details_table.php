<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawl_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crawl_id')->constrained();
            $table->string('http_status');
            $table->string('url');
            $table->integer('unique_images');
            $table->integer('unique_internal_links');
            $table->integer('unique_external_links');
            $table->integer('page_load');
            $table->integer('word_count');
            $table->integer('title_length');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawl_details');
    }
};
