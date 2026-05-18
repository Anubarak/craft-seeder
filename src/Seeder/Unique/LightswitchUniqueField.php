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

use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Field\Lightswitch;

class LightswitchUniqueField implements UniqueFieldInterface
{
    /**
     * @param \CraftCms\Cms\Field\Lightswitch $field
     * @inheritDoc
     */
    public function getDescription(FieldInterface $field): string
    {
        return ($field->offLabel?? 'False') . ' | ' . ($field->onLabel ?? 'True');
    }

    /**
     * @inheritDoc
     */
    public function getValues(FieldInterface $field): array
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