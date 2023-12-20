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

class Color extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        return $this->factory->safeHexColor;
    }
}