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

use Anubarak\Seeder\Seeder\Fields;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Field\PlainText;

class PlainTextUniqueField implements UniqueFieldInterface
{
    /**
     * @inheritDoc
     */
    public function getDescription(FieldInterface $field): string
    {
        return 'Text | empty';
    }

    /**
     * @inheritDoc
     */
    public function getValues(FieldInterface $field): array
    {
        return [
            null,
            fn() => app(Fields::class)->Title()
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