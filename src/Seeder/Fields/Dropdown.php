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
 * Class Dropdown
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Dropdown extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\Dropdown|FieldInterface $field, ElementInterface|null $element = null)
    {
        return $field->options[array_rand($field->options)]['value'];
    }
}