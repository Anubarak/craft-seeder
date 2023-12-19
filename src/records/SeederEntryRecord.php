<?php

namespace anubarak\seeder\records;

use craft\db\ActiveRecord;

/**
 * Class SeederEntryRecord
 *
 * @package anubarak\seeder\records
 * @since   19/12/2023
 * @author  by Robin Schambach
 *
 * @property string entryUid
 * @property int    section
 */
class SeederEntryRecord extends ActiveRecord
{
    // Props
    // =========================================================================

    public static $tableName = '{{%seeder_entries}}';

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName(): string
    {
        return self::$tableName;
    }
}
