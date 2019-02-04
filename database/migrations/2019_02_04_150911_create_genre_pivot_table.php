<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenrePivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genre_to_media', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('model_id');

            $table->uuid('genre_id');
            $table->foreign('genre_id', 'fk_genre_to_media_to_genre_id')
                ->references('id')->on('genres')
                ->onDelete('CASCADE');

            $table->index(['model_id', 'genre_id']);
        });

        DB::statement('ALTER TABLE genre_to_media ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('genre_to_media', function (Blueprint $table) {
            $table->dropForeign('fk_genre_to_media_to_genre_id');
        });

        Schema::dropIfExists('genre_to_media');
    }
}
