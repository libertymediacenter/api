<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audio', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('media_container_id')->index();
            $table->foreign('media_container_id')
                ->references('id')->on('media_containers')
                ->onDelete('CASCADE');

            $table->string('display_name');
            $table->integer('stream_index');

            $table->integer('bitrate')->nullable();
            $table->string('codec')->nullable();
            $table->string('language_code')->nullable();
            $table->integer('channels')->nullable();
            $table->string('profile')->nullable();
            $table->string('channel_layout')->nullable();
            $table->integer('sampling_rate')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio', function (Blueprint $table) {
            $table->dropForeign('media_container_id');
        });

        Schema::dropIfExists('audio');
    }
}
