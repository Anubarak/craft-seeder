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

namespace anubarak\seeder\events;

use craft\base\Event;

/**
 * Class RegisterUniqueFieldEvent
 *
 * @package anubarak\seeder\events
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class RegisterUniqueFieldEvent extends Event
{
    /**
     * @var \anubarak\seeder\services\unique\UniqueFieldInterface[] $fields
     */
    public array $fields = [];
}