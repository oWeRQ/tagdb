<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entities', function (Blueprint $table) {
            $table->index('name');
        });

        Schema::table('entities_tags', function (Blueprint $table) {
            $table->unique(['entity_id', 'tag_id']);
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->unique('name');
        });

        Schema::table('values', function (Blueprint $table) {
            $table->unique(['entity_id', 'field_id']);
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
            $table->dropIndex('entities_name_index');
        });

        Schema::table('entities_tags', function (Blueprint $table) {
            $table->dropUnique('entities_tags_entity_id_tag_id_unique');
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->dropUnique('presets_name_unique');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('tags_name_unique');
        });

        Schema::table('values', function (Blueprint $table) {
            $table->dropUnique('values_entity_id_field_id_unique');
        });
    }
}
