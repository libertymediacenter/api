<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCitextExtension extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "citext";');
    }

    public function down(): void
    {
        DB::statement('DROP EXTENSION IF EXISTS "citext";');
    }
}
