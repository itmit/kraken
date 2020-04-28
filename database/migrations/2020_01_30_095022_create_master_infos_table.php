<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('master_id')->unsigned();
            $table->bigInteger('department_id')->unsigned();
            $table->string('name');
            $table->string('qualification');
            $table->string('work');
            $table->string('phone');
            $table->integer('rating')->default(0);
            $table->enum('status', ['free', 'busy', 'offline'])->default('offline');
            $table->float('latitude', 8, 6)->nullable(); // широта
            $table->float('longitude', 8, 6)->nullable(); // долгота
            $table->enum('way', ['driving', 'walking'])->default(0);
            $table->enum('radius', ['0', '1', '5', '10'])->default('0');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('master_id')->references('id')->on('clients');
            // $table->foreign('department_id')->references('id')->on('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_infos');
    }
}
