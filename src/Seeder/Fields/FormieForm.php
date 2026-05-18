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

namespace Anubarak\Seeder\Seeder\Fields;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use verbb\formie\elements\Form;
use verbb\formie\fields\Forms;

/**
 * Class FormieForm
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   12.07.2024
 * @author  by Robin Schambach
 */
class FormieForm extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface|Forms $field, ElementInterface|null $element = null)
    {
        $limit = 2;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }

        $query = Form::find()
            ->limit(random_int(1, $limit))
            ->inRandomOrder();

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