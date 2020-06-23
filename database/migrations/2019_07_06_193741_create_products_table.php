<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->decimal('price1', 8, 2)->nullable();
            $table->decimal('price2', 8, 2)->nullable();
            $table->decimal('price3', 8, 2)->nullable();
            $table->integer('tax_id')->nullable();
            $table->integer('alert_quantity')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->string('image')->nullable();
            $table->text('detail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
