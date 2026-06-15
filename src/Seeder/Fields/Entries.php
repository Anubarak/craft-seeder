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

use Anubarak\Seeder\Seeder\Fields;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Section\Sections;
use CraftCms\Cms\Site\Sites;

/**
 * Class Entries
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Entries extends BaseField
{
    public function __construct(
        \Faker\Generator $factory,
        Fields $fields,
        private readonly Sections $sections,
        private readonly Sites $sites,
    )
    {
        parent::__construct($factory, $fields);
    }

    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\Entries|FieldInterface $field, ElementInterface|null $element = null)
    {
        $sources = $field->sources;
        if(!is_array($sources)){
            if($sources === '*'){
                $sources = [];
            } else {
                $sources = [$sources];
            }
        }
        $sectionIds = [];
        foreach ($sources as $source) {
            $sectionUid = str_replace('section:', '', $source);
            $sectionIds[] = $this->sections->getSectionByUid($sectionUid)->id;
        }
        if(empty($sectionIds)){
            $sectionIds = null;
        }

        $limit = 2;
        if($field->maxRelations){
            $limit = $field->maxRelations;
        }

        $query = Entry::find()
            ->sectionId($sectionIds)
            ->limit(random_int(1, $limit))
            ->inRandomOrder();

        if($field->targetSiteId){
            $site = $this->sites->getSiteByUid($field->targetSiteId);
            $query->siteId($site->id);
        }elseif ($element && $element->siteId){
            $query->siteId($element->siteId);
        } else {
            $query->siteId($this->sites->getPrimarySite()->id);
        }

        return $query->ids();
    }
}