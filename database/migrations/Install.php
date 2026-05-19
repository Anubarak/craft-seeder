<?php

namespace Anubarak\Seeder\migrations;



use CraftCms\Cms\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Install migration.
 */
class Install extends Migration
{

    public function up(): bool
    {
        if (!Schema::hasTable('seeder_entries')) {
            Schema::create('seeder_entries', function (Blueprint $table) {
                $table->id();
                $table->string('entryUid', 36);
                $table->integer('section');
                $table->dateTime('dateCreated');
                $table->dateTime('dateUpdated');
                $table->string('uid', 36);
            });
        }

        if (!Schema::hasTable('seeder_assets')) {
            Schema::create('seeder_assets', function (Blueprint $table) {
                $table->id();
                $table->string('assetUid', 36);
                $table->dateTime('dateCreated');
                $table->dateTime('dateUpdated');
                $table->string('uid', 36);
            });
        }

        if (!Schema::hasTable('seeder_user')) {
            Schema::create('seeder_user', function (Blueprint $table) {
                $table->id();
                $table->string('userUid', 36);
                $table->dateTime('dateCreated');
                $table->dateTime('dateUpdated');
                $table->string('uid', 36);
            });
        }

        return true;
    }
    
    public function down(): bool
    {
        Schema::dropIfExists('seeder_entry');
        Schema::dropIfExists('seeder_assets');
        Schema::dropIfExists('seeder_user');

        return true;
    }
}
