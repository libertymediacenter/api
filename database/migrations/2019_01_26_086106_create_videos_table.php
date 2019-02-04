<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('media_container_id')->index();
            $table->foreign('media_container_id')
                ->references('id')->on('media_containers')
                ->onDelete('CASCADE');

            $table->string('display_name');
            $table->integer('stream_index');

            $table->integer('bitrate')->nullable();
            $table->string('framerate')->nullable();
            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
            $table->string('codec')->nullable();
            $table->string('chroma_location')->nullable();
            $table->string('color_primaries')->nullable();
            $table->string('color_range')->nullable();
            $table->string('profile')->nullable();
            $table->string('scan_type')->nullable();

            $table->timestamps();
        });

        DB::statement('ALTER TABLE videos ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
