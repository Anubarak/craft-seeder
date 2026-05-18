<?php
/**
 * Craft CMS Plugins for Craft CMS 3.x
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2019 Robin Schambach
 */

namespace Anubarak\Seeder\Events;


/**
 * Class RegisterFieldTypeEvent
 * @since   05.09.2019
 */
class RegisterFieldTypeEvent
{
    /**
     * @param array<class-string> $types
     */
    public function __construct(
        public array $types = []
    )
    {
    }

}