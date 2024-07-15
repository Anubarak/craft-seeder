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
 * Class Money
 *
 * @package anubarak\seeder\services\fields
 * @since   12.07.2024
 * @author  by Robin Schambach
 */
class Money extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        return [
            'value'  => $this->factory->numberBetween(100, 100000),
            'locale' => \Craft::$app->language
        ];
    }
}