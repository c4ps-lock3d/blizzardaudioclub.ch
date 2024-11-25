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
        Schema::table('artistes', function (Blueprint $table) {
            $table->string('bandcamp');
            $table->string('website');
            $table->string('spotify');
            $table->string('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artistes', function (Blueprint $table) {
            $table->dropColumn('bandcamp');
            $table->dropColumn('website');
            $table->dropColumn('spotify');
            $table->dropColumn('country');
        });
    }
};
