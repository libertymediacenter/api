<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('model_id');
            $table->string('model_type');
            $table->unique(['model_id', 'model_type'], 'ratings_morph_unique_idx');

            $table->string('provider_id');

            $table->enum('provider', ['imdb', 'tmdb', 'rottentomatoes', 'metacritic']);
            $table->integer('score');
        });

        DB::statement('ALTER TABLE ratings ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique('ratings_morph_unique_idx');
        });

        Schema::dropIfExists('ratings');
    }
}
