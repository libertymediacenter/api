<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('title')->index();
            $table->text('slug')->unique();
            $table->integer('year')->nullable();
            $table->date('released')->nullable();
            $table->integer('runtime')->nullable();

            $table->text('tagline')->nullable();
            $table->text('summary')->nullable();
            $table->text('plot')->nullable();

            $table->string('poster')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE movies ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
        DB::statement('ALTER TABLE movies ALTER COLUMN title TYPE citext;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies');
    }
}
