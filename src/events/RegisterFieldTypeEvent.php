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

namespace studioespresso\seeder\events;

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
use studioespresso\seeder\Seeder;
use yii\base\Event;

/**
 * Class RegisterFieldTypeEvent
 * @package studioespresso\seeder\Events
 * @since   05.09.2019
 */
class RegisterFieldTypeEvent extends Event
{
    public $types = [];

    public function init()
    {
        parent::init();

        $this->types = [];
        $fieldsService = Seeder::$plugin->fields;
        $this->types[Dropdown::class] = [$fieldsService, 'Dropdown'];
        $this->types[Lightswitch::class] = [$fieldsService, 'Lightswitch'];
        $this->types[Date::class] = [$fieldsService, 'Date'];
        $this->types[PlainText::class] = [$fieldsService, 'PlainText'];
        $this->types[Email::class] = [$fieldsService, 'Email'];
        $this->types[Url::class] = [$fieldsService, 'Url'];
        $this->types[Color::class] = [$fieldsService, 'Color'];
        $this->types[Categories::class] = [$fieldsService, 'Categories'];
        $this->types[Checkboxes::class] = [$fieldsService, 'Checkboxes'];
        $this->types[RadioButtons::class] = [$fieldsService, 'RadioButtons'];
        $this->types[MultiSelect::class] = [$fieldsService, 'MultiSelect'];
        $this->types[Table::class] = [$fieldsService, 'Table'];
        $this->types[Tags::class] = [$fieldsService, 'Tags'];
        $this->types[Entries::class] = [$fieldsService, 'Entries'];
        $this->types[Assets::class] = [$fieldsService, 'Assets'];
        $this->types[Number::class] = [$fieldsService, 'Number'];
        $this->types['craft\\redactor\\Field'] = [Seeder::$plugin->redactor, 'Field'];
    }
}