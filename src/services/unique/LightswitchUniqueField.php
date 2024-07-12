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

use craft\fields\Lightswitch;
use craft\base\Field;

class LightswitchUniqueField implements UniqueFieldInterface
{
    /**
     * @param Lightswitch $field
     * @inheritDoc
     */
    public function getDescription(Field $field): string
    {
        return ($field->offLabel?? 'False') . ' | ' . ($field->onLabel ?? 'True');
    }

    /**
     * @inheritDoc
     */
    public function getValues(Field $field): array
    {
        return [
            true,
            false
        ];
    }

    /**
     * @inheritDoc
     */
    public function getFieldClass(): string
    {
        return Lightswitch::class;
    }
}