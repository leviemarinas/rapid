<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVarianceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variance', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('pvId');
            $table->string('pvProductId');
            $table->string('pvVarianceId');
            $table->double('pvCost',10,2)->nullable();
            $table->integer('pvThreshold');
            $table->boolean('pvIsActive');
            $table->timestamps();
            $table->foreign('pvProductId')
                  ->references('productId')->on('product')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            $table->foreign('pvVarianceId')
                  ->references('varianceId')->on('variance')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variance');
    }
}
