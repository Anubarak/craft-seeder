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

namespace Anubarak\Seeder\Events;

/**
 * Class RegisterUniqueFieldEvent
 *
 * @package Anubarak\Seeder\events
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class RegisterUniqueFieldEvent
{
    public function __construct(
        /**
         * @var \Anubarak\Seeder\services\unique\UniqueFieldInterface[] $fields
         */
        public array $fields = []
    ) {
    }
}