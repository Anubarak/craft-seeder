<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2023 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;

/**
 * Class RadioButtons
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class RadioButtons extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\RadioButtons|FieldInterface $field, ElementInterface $element)
    {
        return $field->options[array_rand($field->options)]['value'];
    }
}