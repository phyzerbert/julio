<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("user_id")->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->string('reference_no')->nullable();
            $table->integer('store_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('biller_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('attachment')->nullable();
            $table->text('note')->nullable();
            $table->integer('status')->nullable();
            $table->decimal('discount', 11, 2)->default(0);
            $table->string('discount_string')->nullable();
            $table->decimal('shipping', 11, 2)->default(0);
            $table->string('shipping_string')->nullable();
            $table->decimal('grand_total', 11, 2)->default(0);
            $table->integer('credit_days')->default(0)->nullable();
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
        Schema::dropIfExists('sales');
    }
}
