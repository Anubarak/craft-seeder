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

namespace anubarak\seeder\services\unique;

use craft\base\Field;
use craft\fields\BaseOptionsField;

/**
 * Class DropdownUniqueField
 *
 * @package anubarak\seeder\services\unique
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class DropdownUniqueField implements UniqueFieldInterface
{
    /**
     * @param \craft\fields\BaseOptionsField $field
     *
     * @inheritDoc
     */
    public function getDescription(Field $field): string
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
    public function getValues(Field $field): array
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