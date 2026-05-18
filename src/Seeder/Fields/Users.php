<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2023 Robin Schambach|Secondred Newmedia GmbH
 */

namespace Anubarak\Seeder\Seeder\Fields;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\User\Elements\User;

/**
 * Class Entries
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Users extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\Users|FieldInterface $field, ElementInterface|null $element = null)
    {
        $limit = 1;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }

        $query = User::find()
            ->limit(random_int(1, $limit))
            ->inRandomOrder();

        return $query->ids();
    }
}