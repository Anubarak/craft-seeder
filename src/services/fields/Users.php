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

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\Db;
use yii\db\Expression;

/**
 * Class Entries
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Users extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Users|FieldInterface $field, ElementInterface|null $element = null)
    {
        $limit = 1;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }

        $query = User::find()
            ->limit(random_int(1, $limit))
            ->orderBy(\anubarak\seeder\helpers\DB::random());

        return $query->ids();
    }
}