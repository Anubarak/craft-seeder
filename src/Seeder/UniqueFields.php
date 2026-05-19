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

namespace Anubarak\Seeder\Seeder;

use Anubarak\Seeder\events\RegisterUniqueFieldEvent;
use Anubarak\Seeder\Seeder\unique\DropdownUniqueField;
use Anubarak\Seeder\Seeder\unique\HtmlUniqueField;
use Anubarak\Seeder\Seeder\unique\HyperUniqueField;
use Anubarak\Seeder\Seeder\unique\LightswitchUniqueField;
use Anubarak\Seeder\Seeder\unique\PlainTextUniqueField;
use Anubarak\Seeder\Seeder\unique\RelationUniqueField;
use Anubarak\Seeder\Seeder\unique\UniqueFieldInterface;
use CraftCms\Cms\Field\Models\Field;
use Illuminate\Container\Attributes\Singleton;

#[Singleton]
class UniqueFields
{
    /**
     * @var \Anubarak\Seeder\Seeder\unique\UniqueFieldInterface[]|null
     */
    protected ?array $uniqueFields = null;

    /**
     * Get unique fields -> this is a list of fields that will be generated uniquely when creating matrix blocks
     *
     * @return \Anubarak\Seeder\Seeder\unique\UniqueFieldInterface[]
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getUniqueFields(): array
    {
        if ($this->uniqueFields !== null) {
            return $this->uniqueFields;
        }

        $fields = [
            DropdownUniqueField::class    => new DropdownUniqueField(),
            HyperUniqueField::class       => new HyperUniqueField(),
            LightswitchUniqueField::class => new LightswitchUniqueField(),
            PlainTextUniqueField::class   => new PlainTextUniqueField(),
            RelationUniqueField::class    => new RelationUniqueField(),
            HtmlUniqueField::class        => new HtmlUniqueField(),
        ];

        event($event = new RegisterUniqueFieldEvent($fields));
        $this->uniqueFields = $event->fields;

        return $this->uniqueFields;
    }

    /**
     * isFieldTypeUnique
     *
     * @param \CraftCms\Cms\Field\Models\Field $field
     *
     * @return bool
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function isFieldTypeUnique(Field $field): bool
    {
        return $this->getUniqueFieldByType($field::class) !== null;
    }

    /**
     * getUniqueFieldByType
     *
     * @param string $fieldClass
     *
     * @return \Anubarak\Seeder\Seeder\unique\UniqueFieldInterface|null
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getUniqueFieldByType(string $fieldClass): ?UniqueFieldInterface
    {
        foreach ($this->getUniqueFields() as $uniqueField) {
            if (is_a($fieldClass, $uniqueField->getFieldClass(), true)) {
                return $uniqueField;
            }
        }

        return null;
    }
}