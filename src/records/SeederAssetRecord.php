<?php

namespace anubarak\seeder\records;

use craft\db\ActiveRecord;

/**
 * Class SeederAssetRecord
 *
 * @package anubarak\seeder\records
 * @since   26.06.2024
 * @author  by Robin Schambach
 * @property string $assetUid
 */
class SeederAssetRecord extends ActiveRecord
{
    // Props
    // =========================================================================

    public static $tableName = '{{%seeder_assets}}';

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
