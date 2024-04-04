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

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;

/**
 * Class Hyper
 *
 * @package anubarak\seeder\services\fields
 * @since   04/04/2024
 * @author  by Robin Schambach
 */
class Hyper extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\verbb\hyper\fields\HyperField|FieldInterface $field, ElementInterface $element = null)
    {
        // TODO: add other link types
        $value = new \verbb\hyper\links\Url();
        $value->linkText = implode(' ', $this->factory->words());
        $value->linkValue = $this->factory->url();
//        $value->fields = [
//            'myCustomField' => 'some value',
//        ];

        return [$value];
    }
}