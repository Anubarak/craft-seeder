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
use craft\base\Field;

/**
 * Class HtmlUniqueField
 *
 * @package anubarak\seeder\services\unique
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class HtmlUniqueField implements UniqueFieldInterface
{
    /**
     * @inheritDoc
     */
    public function getDescription(Field $field): string
    {
        return 'HTML | empty';
    }

    /**
     * @inheritDoc
     */
    public function getValues(Field $field): array
    {
        return [
            null,
            fn() => Seeder::$plugin->getSeeder()->getFieldData($field)
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldClass(): string
    {
        return 'craft\\htmlfield\\HtmlField';
    }
}