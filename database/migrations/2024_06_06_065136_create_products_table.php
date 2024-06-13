<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('size'); // Required field
            $table->string('reference_number')->nullable(); // Nullable field
            $table->decimal('price', 8, 2)->nullable(); // Nullable field
            $table->string('brand'); // Required field
            $table->unsignedBigInteger('category_id'); // Required field
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
