<?php

namespace anubarak\seeder\migrations;

use craft\db\Migration;
use anubarak\seeder\records\SeederAssetRecord;
use anubarak\seeder\records\SeederEntryRecord;
use anubarak\seeder\records\SeederUserRecord;

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

        if (!$this->db->tableExists(SeederEntryRecord::$tableName)) {
            $this->createTable(
                SeederEntryRecord::$tableName,
                [
                    'id'          => $this->primaryKey(),
                    'entryUid'    => $this->uid()->notNull(),
                    'section'     => $this->integer()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid()->notNull(),
                ]
            );
        }

        if (!$this->db->tableExists(SeederAssetRecord::$tableName)) {
            $this->createTable(
                SeederAssetRecord::$tableName,
                [
                    'id'          => $this->primaryKey(),
                    'assetUid'    => $this->uid()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid()->notNull(),
                ]
            );
        }

        if (!$this->db->tableExists(SeederUserRecord::$tableName)) {
            $this->createTable(
                SeederUserRecord::$tableName,
                [
                    'id'          => $this->primaryKey(),
                    'userUid'     => $this->uid()->notNull(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid()->notNull(),
                ]
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable(SeederEntryRecord::$tableName);
        $this->dropTable(SeederAssetRecord::$tableName);
        $this->dropTable(SeederUserRecord::$tableName);

        return true;
    }
}
