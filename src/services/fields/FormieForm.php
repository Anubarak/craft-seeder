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

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use verbb\formie\elements\Form;
use verbb\formie\fields\Forms;

/**
 * Class FormieForm
 *
 * @package anubarak\seeder\services\fields
 * @since   12.07.2024
 * @author  by Robin Schambach
 */
class FormieForm extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface|Forms $field, ElementInterface $element = null)
    {
        $limit = 2;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }

        $query = Form::find()
            ->limit(random_int(1, $limit))
            ->orderBy(\anubarak\seeder\helpers\DB::random());

        if ($field->targetSiteId) {
            $site = \Craft::$app->getSites()->getSiteByUid($field->targetSiteId);
            $query->siteId($site->id);
        } elseif ($element && $element->siteId) {
            $query->siteId($element->siteId);
        } else {
            $query->siteId(\Craft::$app->getSites()->getPrimarySite()->id);
        }

        return $query->ids();
    }
}