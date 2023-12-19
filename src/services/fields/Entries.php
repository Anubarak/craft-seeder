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
use craft\helpers\Db;
use yii\db\Expression;

/**
 * Class Entries
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Entries extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Entries|FieldInterface $field, ElementInterface $element)
    {
        $sources = $field->sources;
        $sectionIds = [];
        foreach ($sources as $source) {
            $sectionUid = str_replace('section:', '', $source);
            $sectionIds[] = Db::idByUid(\craft\db\Table::SECTIONS, $sectionUid);
        }

        $limit = 2;
        if($field->maxRelations){
            $limit = $field->maxRelations;
        }

        $query = Entry::find()
            ->sectionId($sectionIds)
            ->limit(random_int(1, $limit))
            ->orderBy(new Expression('rand()'));

        if($field->targetSiteId){
            $site = \Craft::$app->getSites()->getSiteByUid($field->targetSiteId);
            $query->siteId($site->id);
        }elseif ($element->siteId){
            $query->siteId($element->siteId);
        }

        return $query->ids();
    }
}