<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2024 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\helpers;

use craft\db\Connection;
use yii\db\Expression;

/**
 * Class DB
 *
 * @package anubarak\seeder\helpers
 * @since   13.05.2024
 * @author  by Robin Schambach
 */
class DB
{
    /**
     * random
     *
     * @return void|\yii\db\Expression
     * @author Robin Schambach
     * @since  13.05.2024
     */
    public static function random()
    {
        return match (\Craft::$app->getDb()->getDriverName()){
            Connection::DRIVER_PGSQL => new Expression('random()'),
            Connection::DRIVER_MYSQL => new Expression('rand()'),
        };
    }
}