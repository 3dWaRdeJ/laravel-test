<?php

use App\Position;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;


class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Position::TABLE_NAME, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('level');
            $table->integer('chief_position_id')->nullable()->unsigned();
            $table->integer('admin_create_id')->nullable()->unsigned();
            $table->integer('admin_update_id')->nullable()->unsigned();
            $table->timestamps();

            //foreign keys
            $table
                ->foreign('chief_position_id')
                ->on(Position::TABLE_NAME)
                ->references('id');
            $table
                ->foreign('admin_create_id')
                ->on(User::TABLE_NAME)
                ->references('id');
            $table
                ->foreign('admin_update_id')
                ->on(User::TABLE_NAME)
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
