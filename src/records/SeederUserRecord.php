<?php

namespace anubarak\seeder\records;

use craft\db\ActiveRecord;

/**
 * Class SeederUserRecord
 *
 * @package anubarak\seeder\records
 * @since   15.07.2024
 * @author  by Robin Schambach
 * @property string $userUid
 */
class SeederUserRecord extends ActiveRecord
{

	// Props
	// =========================================================================

	public static string $tableName = '{{%seeder_user}}';

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public static function tableName (): string
	{
		return self::$tableName;
	}
}
