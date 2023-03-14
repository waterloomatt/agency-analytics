<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crawls', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->decimal('avg_page_load');
            $table->decimal('avg_word_count');
            $table->decimal('avg_title_length');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crawls');
    }
};
