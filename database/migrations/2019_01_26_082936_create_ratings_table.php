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
            $table->index(['model_id', 'model_type'], 'ratings_morph_idx');

            $table->string('provider_id');

            $table->enum('provider', ['imdb', 'tmdb', 'rottentomatoes', 'metacritic']);
            $table->decimal('score')->nullable();
            $table->integer('votes')->nullable();
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
            $table->dropIndex('ratings_morph_idx');
        });

        Schema::dropIfExists('ratings');
    }
}
