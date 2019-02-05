<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shows', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('title');
            $table->text('slug');

            $table->text('poster')->nullable();

            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable();

            $table->text('summary')->nullable();
            $table->enum('status', ['Continuing', 'ended', 'cancelled', 'unknown']);

            $table->bigInteger('thetvdb_id');

            $table->timestamps();
        });

        DB::statement('ALTER TABLE shows ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
        DB::statement('ALTER TABLE shows ALTER COLUMN title TYPE citext;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shows');
    }
}
