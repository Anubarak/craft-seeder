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
 * Class MultiSelect
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class MultiSelect extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\MultiSelect|FieldInterface $field, ElementInterface|null $element = null)
    {
        $options = [];
        for ($x = 1, $xMax = random_int(1, count($field->options)); $x <= $xMax; $x++) {
            $options[] = $field->options[array_rand($field->options)]['value'];
        }

        return $options;
    }
}