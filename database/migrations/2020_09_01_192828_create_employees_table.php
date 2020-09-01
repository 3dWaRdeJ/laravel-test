<?php

use App\Employee;
use App\Position;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name');
            $table->float('salary')->unsigned();
            $table->date('start_date');
            $table->string('phone', 15);
            $table->string('email');
            $table->string('photo_path')
                ->nullable()
                ->default(null);
            $table->integer('chief_id')
                ->nullable()
                ->default(null)
                ->unsigned();
            $table->integer('position_id')->unsigned();
            $table->integer('admin_create_id')->unsigned();
            $table->integer('admin_update_id')->unsigned();
            $table->timestamps();

            //foreign keys
            $table
                ->foreign('chief_id')
                ->on(Employee::TABLE_NAME)
                ->references('id');
            $table
                ->foreign('position_id')
                ->on(Position::TABLE_NAME)
                ->references('id');
            $table
                ->foreign('admin_create_id')
                ->on(User::TABLE_NAME)
                ->references('id');
            $table
                ->foreign('admin_update_id')->on(User::TABLE_NAME)
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
        Schema::dropIfExists('employees');
    }
}
