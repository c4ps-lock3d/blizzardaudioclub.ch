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
        Schema::create('artiste_product', function (Blueprint $table) {
                $table->foreignIdFor(\App\Models\Artiste::class)->constrained()->cascadeOnDelete();
                $table->foreignIdFor(\Webkul\Product\Models\Product::class)->constrained()->cascadeOnDelete();
                $table->primary(['artiste_id','product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artiste_product');
    }
};
