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

use anubarak\seeder\Seeder;
use craft\fields\PlainText;
use craft\base\Field;

class PlainTextUniqueField implements UniqueFieldInterface
{
    /**
     * @inheritDoc
     */
    public function getDescription(Field $field): string
    {
        return 'Text | empty';
    }

    /**
     * @inheritDoc
     */
    public function getValues(Field $field): array
    {
        return [
            null,
            fn() => Seeder::$plugin->fields->Title()
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldClass(): string
    {
        return PlainText::class;
    }
}