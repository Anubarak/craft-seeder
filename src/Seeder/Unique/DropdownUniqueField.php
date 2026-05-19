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

namespace Anubarak\Seeder\Seeder\Unique;

use CraftCms\Cms\Field\BaseOptionsField;
use CraftCms\Cms\Field\Contracts\FieldInterface;

/**
 * Class DropdownUniqueField
 *
 * @package Anubarak\Seeder\Seeder\unique
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class DropdownUniqueField implements UniqueFieldInterface
{
    /**
     * @param BaseOptionsField $field
     *
     * @inheritDoc
     */
    public function getDescription(FieldInterface $field): string
    {
        $options = [];
        foreach ($field->options as $option) {
            $options[] = '<code>' . $option['value'] . '</code>';
        }

        return 'Options: ' . join(' | ', $options);
    }

    /**
     * @param BaseOptionsField $field
     *
     * @inheritDoc
     */
    public function getValues(FieldInterface$field): array
    {
        $options = [];
        foreach ($field->options as $option) {
            $options[] = $option['value'];
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function getFieldClass(): string
    {
        return BaseOptionsField::class;
    }
}