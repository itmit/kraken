<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInquiryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inquiry_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('inquiry_id')->unsigned();
            $table->string('work');
            $table->enum('urgency', ['urgent', 'now', 'scheduled']);
            $table->text('description');
            $table->text('address');
            $table->enum('status', ['created', 'performer appointed', 'on performance']); // запрос создан, назначен исполнитель, на исполнении
            $table->dateTime('started_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('inquiry_id')->references('id')->on('inquiries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inquiry_details');
    }
}
