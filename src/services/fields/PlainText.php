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
 * Class PlainText
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class PlainText extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\PlainText|FieldInterface $field, ElementInterface $element = null)
    {
        if(!$field->multiline){
            return $this->factory->text($field->charLimit ?: 200);
        }

        return $this->factory->realText($field->charLimit ?: 200);
    }
}