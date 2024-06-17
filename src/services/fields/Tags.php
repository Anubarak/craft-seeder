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
use craft\elements\Tag;
use craft\helpers\Db;
use yii\db\Expression;

/**
 * Class Entries
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Tags extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Tags|FieldInterface $field, ElementInterface $element = null)
    {
        $source = $field->sources;
        $uid = str_replace('taggroup:', '', $source);
        $groupId = Db::idByUid(\craft\db\Table::TAGGROUPS, $uid);

        $limit = 2;
        if($field->maxRelations){
            $limit = $field->maxRelations;
        }

        $query = Tag::find()
            ->groupId($groupId)
            ->limit(random_int(1, $limit))
            ->orderBy(new Expression('rand()'));

        if($field->targetSiteId){
            $site = \Craft::$app->getSites()->getSiteByUid($field->targetSiteId);
            $query->siteId($site->id);
        }elseif ($element && $element->siteId){
            $query->siteId($element->siteId);
        } else {
            $query->siteId(\Craft::$app->getSites()->getPrimarySite()->id);
        }

        return $query->ids();
    }
}