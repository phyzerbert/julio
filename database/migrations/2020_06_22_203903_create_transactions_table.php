<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('reference_no')->nullable();
            $table->dateTime('timestamp')->useCurrent();
            $table->integer('type')->nullable();
            $table->integer('tcategory_id')->nullable();
            $table->string('supplier_customer')->nullable();
            $table->integer('payment_id')->nullable();
            $table->decimal('amount', 11, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('attachment')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
