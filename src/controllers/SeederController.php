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
use anubarak\seeder\services\UniqueFields;
use Craft;
use craft\db\Query;
use craft\db\Table;
use craft\errors\ElementException;
use craft\fieldlayoutelements\CustomField;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\PlainText;
use craft\models\EntryType;
use craft\web\Controller;
use yii\web\HttpException;
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
    public function actionElementMatrixModal(UniqueFields $uniqueService): Response
    {
        $elementId = $this->request->getQueryParam('elementId');
        $element = Craft::$app->getElements()->getElementById($elementId);
        $matrixFields = [];

        foreach ($element->getFieldLayout()?->getCustomFields() as $field) {
            if ($field instanceof Matrix) {
                foreach ($field->getEntryTypes() as $entryType) {
                    $subFields = [];
                    foreach ($entryType->getFieldLayout()->getCustomFields() as $entryTypeField) {
                        $uniqueField = $uniqueService->getUniqueFieldByType($entryTypeField::class);
                        if ($uniqueField) {
                            $subFields[] = [
                                'field'       => $entryTypeField,
                                'description' => $uniqueField->getDescription($entryTypeField)
                            ];
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
     * actionElementContentModal
     *
     * @return \yii\web\Response
     * @throws \yii\web\HttpException
     * @author Robin Schambach
     * @since  08.07.2024
     */
    public function actionElementContentModal(): Response
    {
        $ids = [];
        $elementId = $this->request->getQueryParam('elementId');
        if ($elementId) {
            $ids = [$elementId];
        }
        $elementIds = $this->request->getQueryParam('elementIds');
        if ($elementIds) {
            $ids = $elementIds;
        }

        if (empty($ids)) {
            throw new HttpException(400, 'required ids are missing');
        }


        $handledLayouts = [];

        $data = [];
        foreach ($this->getElementsByIds($ids) as $element) {
            $layout = $element->getFieldLayout();
            if (!$layout) {
                continue;
            }

            if (\in_array($layout->id, $handledLayouts, true)) {
                continue;
            }
            $handledLayouts[] = $layout->id;

            foreach ($layout->getTabs() as $tab) {
                $d = [
                    'tab'    => $tab->name,
                    'fields' => []
                ];
                foreach ($tab->getElements() as $fieldLayoutElement) {
                    if ($fieldLayoutElement instanceof CustomField) {
                        $d['fields'][] = $fieldLayoutElement;
                    }
                }
                $data[] = $d;
            }
        }

        return $this->asCpScreen()
            ->contentTemplate('element-seeder/generateContent.twig', [
                'elementIds' => $ids,
                'fieldData'  => $data,
            ]);
    }

    /**
     * actionGenerateContent
     *
     * @return \yii\web\Response
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @throws \yii\web\MethodNotAllowedHttpException
     * @author Robin Schambach
     * @since  08.07.2024
     */
    public function actionGenerateContent(): Response
    {
        $this->requirePostRequest();
        $elementIds = $this->request->getBodyParam('elementIds');
        $elements = $this->getElementsByIds($elementIds);

        $seeder = Seeder::$plugin->getSeeder();

        $fields = $this->request->getBodyParam('fields');
        $fieldHandles = [];
        foreach ($fields as $handle => $value) {
            if ((bool) $value) {
                $fieldHandles[] = $handle;
            }
        }

        $transaction = Craft::$app->getDb()->beginTransaction();
        $elementService = Craft::$app->getElements();
        try {
            foreach ($elements as $element) {
                $seeder->populateFields($element, $fieldHandles);
                if (!$elementService->saveElement($element)) {
                    throw new ElementException($element, 'Could not save element due to validation errors');
                }
            }
            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }

        return $this->asSuccess('Content generated successfully');
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
    public function actionGenerateMatrix(UniqueFields $uniqueService): Response
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

                if ($fields) {
                    $uniqueBlocks = $this->createUniqueBlocks($entryType, $fields, $uniqueService, $i);
                    foreach ($uniqueBlocks as $key => $block) {
                        $fieldValue[$key] = $block;
                    }
                } else {
                    // add a number of blocks
                    $nr = $blockTypeConfig['number'] ?? null;
                    if ($nr) {
                        for ($x = 0; $x < $nr; $x++) {
                            $fieldValue['new' . $i] = $seeder->getSerializedEntryData($entryType);
                            $i++;
                        }
                    }
                }
            }

            $ids = $element->getFieldValue($matrixField->handle)->ids();
            foreach ($fieldValue as $key => $item) {
                $ids[] = $key;
            }

            $element->setFieldValue($matrixField->handle, [
                'sortOrder' => $ids,
                'entries'   => $fieldValue
            ]);
        }

        if (!Craft::$app->getElements()->saveElement($element)) {
            return $this->asModelFailure($element);
        }

        return $this->asSuccess('Update erfolgreich', $uniqueFields);
    }

    /**
     * createUniqueBlocks
     *
     * @param \craft\models\EntryType       $blockType
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
    public function createUniqueBlocks(EntryType $blockType, array $fields, UniqueFields $uniqueFields, &$i): array
    {
        $uniques = [];
        foreach ($fields as $field) {

            $uniqueField = $uniqueFields->getUniqueFieldByType($field::class);
            if($uniqueField !== null){
                $uniques[$field->handle] = $uniqueField->getValues($field);
            }
        }

        $seeder = Seeder::$plugin->getSeeder();
        $allCombinations = [...$this->getAllCombinations($uniques)];
        $fieldValue = [];
        foreach ($allCombinations as $key => $combination) {
            $f = [];
            foreach ($fields as $j => $field) {
                $v = is_callable($combination[$j]) ? $combination[$j]() : $combination[$j];

                $f[$field->handle] = $v;
            }

            // add the rest of the fields
            foreach ($blockType->getFieldLayout()->getCustomFields() as $customField) {
                // skip if it is already there
                if (array_key_exists($customField->handle, $f)) {
                    continue;
                }

                $v = $seeder->getFieldData($customField);
                if ($v) {
                    $f[$customField->handle] = $v;
                }
            }

            $fieldValue['new' . $i] = [
                'type'   => $blockType->handle,
                'title'  => $blockType->hasTitleField ? Seeder::$plugin->fields->Title() : null,
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

    /**
     * getElementsByIds
     *
     * @param array $ids
     *
     * @return \craft\base\ElementInterface[]
     * @author Robin Schambach
     * @since  09.07.2024
     */
    protected function getElementsByIds(array $ids): array
    {
        // we always use the same element type
        $class = (new Query())
            ->select(['type'])
            ->from([Table::ELEMENTS])
            ->where(['id' => $ids[0]])
            ->scalar();

        $query = Craft::$app->getElements()->createElementQuery($class);

        return $query->status(null)->id($ids)->all();
    }
}
