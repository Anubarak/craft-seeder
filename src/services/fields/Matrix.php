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
use anubarak\seeder\Seeder;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Entry;

/**
 * Class Matrix
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Matrix extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Matrix|FieldInterface $field, ElementInterface $element = null)
    {
        $types = $field->getEntryTypes();

        $typeIds = [];
        $types = array_map(
            static function ($type) {
                return $type->id;
            }, $types);

        if (Seeder::getInstance()->getSettings()->eachMatrixBlock) {
            $blockCount = count($types);
            for ($x = 0; $x < $blockCount; $x++) {
                $typeIds[] = $types[$x];
            }
            shuffle($typeIds);
        } else {
            $blockCount = random_int(!empty($field->minBlocks) ? $field->minBlocks : 1, !empty($field->maxBlocks) ? $field->maxBlocks : 6);
            for ($x = 1; $x <= $blockCount; $x++) {
                $typeIds[] = $types[array_rand($types, 1)];
            }
        }

        $elements = \Craft::$app->getElements();
        foreach ($typeIds as $typeId) {
            $matrixBlock = new Entry();
            $matrixBlock->setTypeId($typeId);
            $matrixBlock->fieldId = $field->id;
            if($element){
                $matrixBlock->ownerId = $element->id;
            }
            $elements->saveElement($matrixBlock);
            $matrixBlock = Seeder::$plugin->seeder->populateFields($matrixBlock);
            $elements->saveElement($matrixBlock);
        }

        return null;
    }
}