<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_containers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('path');

            $table->string('container')->nullable();
            $table->bigInteger('bitrate')->nullable();
            $table->integer('duration')->nullable();
            $table->bigInteger('size')->nullable();

            $table->string('media_type');
            $table->uuid('media_id');

            $table->index(["media_type", "media_id"]);

            $table->timestamps();
        });

        DB::statement('ALTER TABLE media_containers ALTER COLUMN id SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_containers');
    }
}
