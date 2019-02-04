<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCastTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cast', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('media_id');

            $table->uuid('people_id');
            $table->foreign('people_id', 'fk_cast_to_people')
                ->references('id')->on('people')
                ->onDelete('CASCADE');

            $table->text('role');

            $table->index(['media_id', 'people_id']);
        });

        DB::statement('ALTER TABLE "cast" ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cast', function (Blueprint $table) {
            $table->dropForeign('fk_cast_to_people');
        });

        Schema::dropIfExists('cast');
    }
}
