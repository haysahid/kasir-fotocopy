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
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('code')->nullable();
            $table->string('name');
            $table->string('description', 2500)->nullable();
            $table->integer('purchase_price');
            $table->integer('selling_price');
            $table->integer('initial_stock');
            $table->string('unit');
            $table->string('category')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->bigInteger('supplier_id')->nullable();
            $table->boolean('from_community')->default(false);
            $table->bigInteger('store_id')->unsigned();
            $table->timestamp('disabled_at')->nullable();

            $table->foreign('store_id')->references('id')->on('stores')->onUpdate('cascade')->onDelete('no action');

            $table->softDeletes();
            $table->timestamps();
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
