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

namespace anubarak\seeder\events;

use craft\fields\Assets;
use craft\fields\Categories;
use craft\fields\Checkboxes;
use craft\fields\Color;
use craft\fields\Date;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Entries;
use craft\fields\Lightswitch;
use craft\fields\MultiSelect;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Table;
use craft\fields\Tags;
use craft\fields\Url;
use anubarak\seeder\Seeder;
use yii\base\Event;

/**
 * Class RegisterFieldTypeEvent
 * @package studioespresso\seeder\Events
 * @since   05.09.2019
 */
class RegisterFieldTypeEvent extends Event
{
    /**
     * @var string[]
     */
    public array $types = [];
}