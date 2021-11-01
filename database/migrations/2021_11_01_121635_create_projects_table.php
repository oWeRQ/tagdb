<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('name');
            $table->timestamps();
        });

        Schema::table('entities', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained();
            $table->dropUnique(['name']);
            $table->index('name');
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained();
            $table->dropUnique(['name']);
            $table->index('name');
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
            $table->dropColumn(['project_id']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['project_id']);
            $table->dropIndex(['name']);
            $table->unique('name');
        });

        Schema::table('presets', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['project_id']);
            $table->dropIndex(['name']);
            $table->unique('name');
        });

        Schema::dropIfExists('projects');
    }
}
