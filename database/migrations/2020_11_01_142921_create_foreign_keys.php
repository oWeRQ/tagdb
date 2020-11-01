<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->change();
            $table->unsignedBigInteger('tag_id')->change();
        });

        Schema::table('entities_tags', function (Blueprint $table) {
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id')->change();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });

        Schema::table('values', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->change();
            $table->unsignedBigInteger('field_id')->change();
        });

        Schema::table('values', function (Blueprint $table) {
            $table->foreign('entity_id')->references('id')->on('entities')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entities_tags', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropForeign(['tag_id']);
        });

        Schema::table('entities_tags', function (Blueprint $table) {
            $table->integer('entity_id')->change();
            $table->integer('tag_id')->change();
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->dropForeign(['tag_id']);
        });

        Schema::table('fields', function (Blueprint $table) {
            $table->integer('tag_id')->change();
        });

        Schema::table('values', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropForeign(['field_id']);
        });

        Schema::table('values', function (Blueprint $table) {
            $table->integer('entity_id')->change();
            $table->integer('field_id')->change();
        });
    }
}
