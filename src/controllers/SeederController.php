<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Seeder
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 studioespresso
 */

namespace anubarak\seeder\controllers;

use anubarak\seeder\records\SeederAssetRecord;
use anubarak\seeder\records\SeederEntryRecord;
use anubarak\seeder\records\SeederUserRecord;
use anubarak\seeder\Seeder;
use Craft;
use craft\fields\BaseOptionsField;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\models\EntryType;
use craft\web\Controller;
use yii\web\Response;

/**
 * @author    studioespresso
 * @package   Seeder
 * @since     1.0.0
 */
class SeederController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $data = [];
        $sections = Craft::$app->getEntries();
        foreach ($sections->getAllSections() as $section) {
            $seededEntries = SeederEntryRecord::findAll([
                'section' => $section->id
            ]);
            if (count($seededEntries)) {
                $data['sections'][$section->id]['id'] = $section->id;
                $data['sections'][$section->id]['name'] = $section->name;
                $data['sections'][$section->id]['count'] = count($seededEntries);
            }
        }

        $seededAssets = SeederAssetRecord::find();
        if ($seededAssets->count()) {
            $data['assets']['count'] = $seededAssets->count();
        }

        $seededUsers = SeederUserRecord::find();
        if ($seededUsers->count()) {
            $data['users']['count'] = $seededUsers->count();
        }


        return $this->renderTemplate('element-seeder/index', ['data' => $data]);
    }

    public function actionClean()
    {
        $data = Craft::$app->request->post('data');
        if ($data) {
            if (!empty($data['sections'])) {
                foreach ($data['sections'] as $sectionId) {
                    Seeder::$plugin->weeder->entries($sectionId);
                }
            }
            if (!empty($data['assets'])) {
                Seeder::$plugin->weeder->assets();
            }
            if (!empty($data['users'])) {
                Seeder::$plugin->weeder->users();
            }
        }

        return $this->redirectToPostedUrl();
    }

    /**
     * actionElementMatrixModal
     *
     * @return \craft\web\Response
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function actionElementMatrixModal(): Response
    {
        $elementId = $this->request->getQueryParam('elementId');
        $element = Craft::$app->getElements()->getElementById($elementId);
        $matrixFields = [];
        foreach ($element->getFieldLayout()?->getCustomFields() as $field) {
            if ($field instanceof Matrix) {
                foreach ($field->getEntryTypes() as $entryType) {
                    $subFields = [];
                    foreach ($entryType->getFieldLayout()->getCustomFields() as $entryTypeField) {
                        if ($entryTypeField instanceof BaseOptionsField || $entryTypeField instanceof Lightswitch) {
                            $subFields[] = $entryTypeField;
                        }
                    }

                    if (!isset($matrixFields[$field->id])) {
                        $matrixFields[$field->id] = [
                            'field'  => $field,
                            'blocks' => []
                        ];
                    }

                    $matrixFields[$field->id]['blocks'][] = [
                        'block'  => $entryType,
                        'fields' => $subFields
                    ];
                }
            }
        }

        return $this->asCpScreen()
            ->contentTemplate('element-seeder/sidebar.twig', [
                'matrixFields' => $matrixFields,
                'elementId'    => $elementId
            ]);
    }

    /**
     * actionGenerateMatrix
     *
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \craft\errors\FieldNotFoundException
     * @throws \craft\errors\InvalidFieldException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\BadRequestHttpException
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public function actionGenerateMatrix(): Response
    {
        $this->requirePostRequest();
        $elementId = $this->request->getBodyParam('elementId');
        $element = Craft::$app->getElements()->getElementById($elementId);

        $seeder = Seeder::$plugin->getSeeder();

        $uniqueFields = $this->request->getBodyParam('uniqueFields');
        $i = 1;
        foreach ($uniqueFields as $matrixFieldId => $value) {
            /** @var Matrix $matrixField */
            $matrixField = Craft::$app->getFields()->getFieldById($matrixFieldId);

            $fieldValue = [];
            foreach ($matrixField->getEntryTypes() as $entryType) {
                $fields = [];
                $blockTypeConfig = $value[$entryType->id] ?? null;
                if (!$blockTypeConfig) {
                    continue;
                }

                foreach ($entryType->getFieldLayout()->getCustomFields() as $blockTypeField) {
                    $blockTypeFieldConfig = $blockTypeConfig['fields'][$blockTypeField->id] ?? null;
                    if (!$blockTypeFieldConfig) {
                        continue;
                    }
                    $fields[] = $blockTypeField;
                }

                if($fields){
                    $uniqueBlocks = $this->createUniqueBlocks($entryType, $fields, $i);
                    foreach ($uniqueBlocks as $key => $block){
                        $fieldValue[$key] = $block;
                    }
                } else {
                    // add a number of blocks
                    $nr = $blockTypeConfig['number'] ?? null;
                    if ($nr) {
                        for($x = 0; $x < $nr; $x++){
                            // just add random blocks
                            $f = [];
                            foreach ($entryType->getFieldLayout()->getCustomFields() as $blockTypeField) {
                                $v = $seeder->getFieldData($blockTypeField);
                                if($v){
                                    $f[$blockTypeField->handle] = $v;
                                }
                            }

                            $fieldValue['new:' . $i] = [
                                'type' => $entryType->handle,
                                'title' => Seeder::$plugin->fields->Title(),
                                'fields' => $f
                            ];
                            $i++;
                        }
                    }
                }
            }

            $ids = $element->getFieldValue($matrixField->handle)->ids();
            foreach ($fieldValue as $key => $item){
                $ids[] =$key;
            }

            $element->setFieldValue($matrixField->handle, [
                'sortOrder' => $ids,
                'entries' => $fieldValue
            ]);
        }

        if(!Craft::$app->getElements()->saveElement($element)){
            return $this->asModelFailure($element);
        }

        return $this->asSuccess('Update erfolgreich', $uniqueFields);
    }

    /**
     * createUniqueBlocks
     *
     * @param \craft\models\EntryType $blockType
     * @param array                         $fields
     * @param                               $i
     *
     * @return array
     * @throws \craft\errors\FieldNotFoundException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function createUniqueBlocks(EntryType $blockType, array $fields, &$i): array
    {
        $uniques = [];
        foreach ($fields as $field) {
            switch (true) {
                case $field instanceof Lightswitch:
                    $uniques[$field->handle] = [
                        true,
                        false
                    ];
                    break;
                case $field instanceof BaseOptionsField:
                    $options = [];
                    foreach ($field->options as $option) {
                        $options[] = $option['value'];
                    }

                    $uniques[$field->handle] = $options;
                    break;
            }
        }

        $seeder = Seeder::$plugin->getSeeder();
        $allCombinations = [...$this->getAllCombinations($uniques)];
        $fieldValue = [];
        foreach ($allCombinations as $key => $combination) {
            $f = [];
            foreach ($fields as $j => $field) {
                $f[$field->handle] = $combination[$j];
            }

            // add the rest of the fields
            foreach ($blockType->getFieldLayout()->getCustomFields() as $customField) {
                // skip if it is already there
                if (isset($f[$customField->handle])) {
                    continue;
                }

                $v = $seeder->getFieldData($customField);
                if ($v) {
                    $f[$customField->handle] = $v;
                }
            }

            $fieldValue['new' . $i] = [
                'type' => $blockType->handle,
                'fields' => $f
            ];
            $i++;
        }

        return $fieldValue;
    }

    /**
     * getAllCombinations
     *
     * @param array $arrays
     *
     * @return iterable
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function getAllCombinations(array $arrays): iterable
    {
        if ($arrays === []) {
            yield [];
            return;
        }
        $head = array_shift($arrays);
        foreach ($head as $element) {
            foreach ($this->getAllCombinations($arrays) as $combination) {
                yield [$element, ...$combination];
            }
        }
    }
}
