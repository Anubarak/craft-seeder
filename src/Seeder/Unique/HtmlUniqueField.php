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

use Anubarak\Seeder\Seeder\Seeder;
use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Field\Models\Field;

/**
 * Class HtmlUniqueField
 *
 * @package Anubarak\Seeder\Seeder\unique
 * @since   10.07.2024
 * @author  by Robin Schambach
 */
class HtmlUniqueField implements UniqueFieldInterface
{
    /**
     * @inheritDoc
     */
    public function getDescription(FieldInterface $field): string
    {
        return 'HTML | empty';
    }

    /**
     * @inheritDoc
     */
    public function getValues(FieldInterface $field): array
    {
        return [
            null,
            fn() => app(Seeder::class)->getFieldData($field)
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