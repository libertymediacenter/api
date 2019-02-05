<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpisodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->bigInteger('thetvdb_id');
            $table->string('imdb_id')->nullable();

            $table->uuid('season_id')->index();
            $table->foreign('season_id')
                ->references('id')->on('seasons')
                ->onDelete('CASCADE');

            $table->string('title')->nullable();
            $table->text('summary')->nullable();
            $table->integer('runtime')->nullable();

            $table->string('poster')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE episodes ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
        DB::statement('ALTER TABLE episodes ALTER COLUMN title TYPE citext;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('episodes');
    }
}
