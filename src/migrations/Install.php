<?php

namespace anubarak\seeder\migrations;

use Craft;
use craft\db\Migration;
use anubarak\seeder\records\SeederAssetRecord;
use anubarak\seeder\records\SeederCategoryRecord;
use anubarak\seeder\records\SeederEntryRecord;
use anubarak\seeder\records\SeederUserRecord;
use yii\db\Schema;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        $this->createTable(
            SeederEntryRecord::$tableName, [
            'id' => $this->primaryKey(),
            'entryUid' => $this->uid()->notNull(),
            'section' => $this->integer()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()->notNull(),
        ]);

        $this->createTable(
            SeederCategoryRecord::$tableName, [
            'id' => $this->primaryKey(),
            'categoryUid' => $this->uid()->notNull(),
            'section' => $this->integer()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()->notNull(),
        ]);

        $this->createTable(
            SeederAssetRecord::$tableName, [
            'id' => $this->primaryKey(),
            'assetUid' => $this->uid()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()->notNull(),
        ]);

        $this->createTable(
            SeederUserRecord::$tableName, [
            'id' => $this->primaryKey(),
            'userUid' => $this->uid()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()->notNull(),
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(SeederEntryRecord::$tableName);
        $this->dropTable(SeederCategoryRecord::$tableName);
        $this->dropTable(SeederAssetRecord::$tableName);
        $this->dropTable(SeederUserRecord::$tableName);
    }
}
