<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLibrariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('libraries', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('name');
            $table->enum('type', ['movie', 'tv', 'sports', 'other']);
            $table->text('metadata_lang')->default('en');

            $table->timestamps();
        });

        DB::statement('ALTER TABLE libraries ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('libraries', function (Blueprint $table) {
            $table->dropUnique('libraries_morph_idx');
        });

        Schema::dropIfExists('libraries');
    }
}
