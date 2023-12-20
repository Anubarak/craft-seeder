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
use Craft;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\MatrixBlock;

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
        $types = $field->getBlockTypes();

        $blockIds = [];
        $types = array_map(
            static function ($type) {
                return $type->id;
            }, $types);

        if (Seeder::getInstance()->getSettings()->eachMatrixBlock) {
            $blockCount = count($types);
            for ($x = 0; $x < $blockCount; $x++) {
                $blockIds[] = $types[$x];
            }
            shuffle($blockIds);
        } else {
            $blockCount = random_int(!empty($field->minBlocks) ? $field->minBlocks : 1, !empty($field->maxBlocks) ? $field->maxBlocks : 6);
            for ($x = 1; $x <= $blockCount; $x++) {
                $blockIds[] = $types[array_rand($types, 1)];
            }
        }

        foreach ($blockIds as $blockId) {
            $type = Craft::$app->matrix->getBlockTypeById($blockId);
            $matrixBlock = new MatrixBlock();
            $matrixBlock->typeId = $type->id;
            $matrixBlock->fieldId = $field->id;
            if($element){
                $matrixBlock->ownerId = $element->id;
            }
            Craft::$app->elements->saveElement($matrixBlock);
            $matrixBlock = Seeder::$plugin->seeder->populateFields($matrixBlock);
            Craft::$app->elements->saveElement($matrixBlock);
        }

        return null;
    }
}