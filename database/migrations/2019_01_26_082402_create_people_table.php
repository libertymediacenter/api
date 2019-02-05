<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('imdb_id');

            $table->text('name');
            $table->text('slug')->nullable();

            $table->text('photo')->nullable();
            $table->text('bio')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE people ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
        DB::statement('ALTER TABLE people ALTER COLUMN "name" TYPE citext;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people');
    }
}
