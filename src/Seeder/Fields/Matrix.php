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

use Anubarak\Seeder\Seeder\Seeder;
use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Entry\Data\EntryType;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use Illuminate\Support\Collection;

/**
 * Class Matrix
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Matrix extends BaseField
{
    public function __construct(
        \Faker\Generator $factory,
        Fields $fields,
        private readonly Seeder $seeder,
    )
    {
        parent::__construct($factory, $fields);
    }

    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\Matrix|FieldInterface $field, ElementInterface|null $element = null)
    {
        $types = $field->getEntryTypes();
        $typeCollection = Collection::make($field->getEntryTypes());

        $typeIds = [];
        $types = array_map(
            static function($type) {
                return $type->id;
            },
            $types
        );

        if (SeederServiceProvider::getInstance()->getSettings()->eachMatrixBlock) {
            $blockCount = count($types);
            for ($x = 0; $x < $blockCount; $x++) {
                $typeIds[] = $types[$x];
            }
            shuffle($typeIds);
        } else {
            $blockCount = random_int(
                !empty($field->minBlocks) ? $field->minBlocks : 1,
                !empty($field->maxBlocks) ? $field->maxBlocks : 6
            );
            for ($x = 1; $x <= $blockCount; $x++) {
                $typeIds[] = $types[array_rand($types, 1)];
            }
        }

        $ids = $element ? $element->getFieldValue($field->handle)->ids() : [];
        $entries = [];

        foreach ($typeIds as $i => $typeId) {
            /** @var EntryType $realType */
            $realType = $typeCollection->where(fn(EntryType $type) => $type->id === $typeId)->first();
            $ids[] = $newId = 'new' . ($i+1);

            $matrixBlock = new Entry();
            $matrixBlock->setTypeId($typeId);
            $matrixBlock->fieldId = $field->id;
            if($element){
                $matrixBlock->setOwner($element);;
            }

            $entries[$newId] = $this->seeder->getSerializedEntryData($realType, $matrixBlock);

            //            $matrixBlock = new Entry();
            //            $matrixBlock->setTypeId($typeId);
            //            $matrixBlock->fieldId = $field->id;
            //            if($element){
            //                $matrixBlock->ownerId = $element->id;
            //            }
            //
            //            if($matrixBlock->getType()->hasTitleField){
            //                $matrixBlock->title =  Seeder::$plugin->fields->Title();
            //            }
            //            $elements->saveElement($matrixBlock);
            //            $matrixBlock = Seeder::$plugin->seeder->populateFields($matrixBlock);
            //            $elements->saveElement($matrixBlock);
        }

        return [
            'sortOrder' => $ids,
            'entries'   => $entries
        ];
    }
}