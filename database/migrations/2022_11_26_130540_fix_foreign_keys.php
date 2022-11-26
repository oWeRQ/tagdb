<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('project_user', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });

        Schema::table('token_preset', function (Blueprint $table) {
            $table->dropForeign(['preset_id']);
            $table->foreign('preset_id')->references('id')->on('presets')->cascadeOnDelete();

            $table->dropForeign(['token_id']);
            $table->foreign('token_id')->references('id')->on('tokens')->cascadeOnDelete();
        });

        Schema::table('tokens', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
        });

        Schema::table('project_user', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
        });

        Schema::table('token_preset', function (Blueprint $table) {
            $table->dropForeign(['preset_id']);
            $table->foreign('preset_id')->references('id')->on('presets')->restrictOnDelete();

            $table->dropForeign(['token_id']);
            $table->foreign('token_id')->references('id')->on('tokens')->restrictOnDelete();
        });

        Schema::table('tokens', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->foreign('project_id')->references('id')->on('projects')->restrictOnDelete();
        });
    }
}
