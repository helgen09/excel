<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportLogsTable extends Migration
{
    public function up()
    {
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('row_number');
            $table->text('errors');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_logs');
    }
}