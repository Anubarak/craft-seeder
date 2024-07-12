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

namespace anubarak\seeder\services;

use anubarak\seeder\events\RegisterUniqueFieldEvent;
use anubarak\seeder\services\unique\DropdownUniqueField;
use anubarak\seeder\services\unique\HtmlUniqueField;
use anubarak\seeder\services\unique\HyperUniqueField;
use anubarak\seeder\services\unique\LightswitchUniqueField;
use anubarak\seeder\services\unique\PlainTextUniqueField;
use anubarak\seeder\services\unique\RelationUniqueField;
use anubarak\seeder\services\unique\UniqueFieldInterface;
use craft\base\Field;
use yii\base\Component;

class UniqueFields extends Component
{
    /**
     * Event to register unique fields
     */
    public const EVENT_REGISTER_UNIQUE_FIELDS = 'registerUniqueFieldsEvent';
    /**
     * @var \anubarak\seeder\services\unique\UniqueFieldInterface[]|null
     */
    protected ?array $uniqueFields = null;

    /**
     * Get unique fields -> this is a list of fields that will be generated uniquely when creating matrix blocks
     *
     * @return \anubarak\seeder\services\unique\UniqueFieldInterface[]
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

        $event = new RegisterUniqueFieldEvent([
            'fields' => $fields
        ]);
        if ($this->hasEventHandlers(self::EVENT_REGISTER_UNIQUE_FIELDS)) {
            $this->trigger(self::EVENT_REGISTER_UNIQUE_FIELDS, $event);
        }
        $this->uniqueFields = $event->fields;

        return $this->uniqueFields;
    }

    /**
     * isFieldTypeUnique
     *
     * @param \craft\base\Field $field
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
     * @return \anubarak\seeder\services\unique\UniqueFieldInterface|null
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