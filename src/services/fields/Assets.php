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
use craft\elements\Asset;
use craft\helpers\Db;
use yii\db\Expression;

/**
 * Class Assets
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Assets extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Assets|FieldInterface $field, ElementInterface $element = null)
    {
        $source = $field->sources;
        $volumeIds = [];
        if ($source !== '*') {

            if (!is_array($source)) {
                $source = [$source];
            }

            foreach ($source as $s) {
                $volumeUid = str_replace('folder:', '', $s);
                $volumeIds[] = Db::idByUid(\craft\db\Table::VOLUMES, $volumeUid);
            }
        }

        $limit = 2;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }


        $query = Asset::find()
            ->limit(random_int(1, $limit))
            ->orderBy(new Expression('rand()'));
        if ($volumeIds) {
            $query->volumeId($volumeIds);
        }

        if ($field->allowedKinds) {
            $query->kind($field->allowedKinds);
        }

        return $query->ids();
    }
}