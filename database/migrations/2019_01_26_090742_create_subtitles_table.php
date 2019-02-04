<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubtitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subtitles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('media_container_id')->index();
            $table->foreign('media_container_id')
                ->references('id')->on('media_containers')
                ->onDelete('CASCADE');

            $table->string('codec')->nullable();
            $table->string('language_code')->nullable();
            $table->string('display_title')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE subtitles ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subtitles');
    }
}
