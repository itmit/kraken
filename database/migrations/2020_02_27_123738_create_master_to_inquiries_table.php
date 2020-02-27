<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterToInquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_to_inquiries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('inquiry_id')->unsigned();
            $table->bigInteger('master_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inquiry_id')->references('id')->on('inquiries');
            $table->foreign('master_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_to_inquiries');
    }
}
