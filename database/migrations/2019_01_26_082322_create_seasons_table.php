<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('show_id')->index();
            $table->foreign('show_id')
                ->references('id')->on('shows')
                ->onDelete('CASCADE');

            $table->integer('season');
            $table->string('poster')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE seasons ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seasons');
    }
}
