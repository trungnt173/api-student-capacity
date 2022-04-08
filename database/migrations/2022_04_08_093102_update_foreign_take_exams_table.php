<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('take_exams', function (Blueprint $table) {
            $table->dropForeign('take_exams_exam_id_foreign');
            $table->foreign('exam_id')->references('id')->on('exams')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('take_exams', function (Blueprint $table) {
            $table->foreign('exam_id')->references('id')->on('exams')->onUpdate('cascade')->onUpdate('cascade');

        });
    }
};
