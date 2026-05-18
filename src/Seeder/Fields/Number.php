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

namespace Anubarak\Seeder\Seeder\Fields;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;

/**
 * Class Number
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Number extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\Number|FieldInterface $field, ElementInterface|null $element = null)
    {
        $min = $field->min ?? 0;
        $max = $field->max ?? 100;

        return random_int($min, $max);
    }
}