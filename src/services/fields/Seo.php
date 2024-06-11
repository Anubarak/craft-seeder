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
 * Class Seo
 *
 * @package anubarak\seeder\services\fields
 * @since   11.06.2024
 * @author  by Robin Schambach
 */
class Seo extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        return null;
    }
}