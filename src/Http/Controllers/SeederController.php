<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Seeder
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 studioespresso
 */

namespace Anubarak\Seeder\Http\Controllers;

use Anubarak\Seeder\records\SeederAssetRecord;
use Anubarak\Seeder\records\SeederEntryRecord;
use Anubarak\Seeder\records\SeederUserRecord;
use Anubarak\Seeder\Seeder\Seeder;
use Anubarak\Seeder\Seeder\UniqueFields;
use Anubarak\Seeder\Seeder\Weeder;
use CraftCms\Cms\Database\Table;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Element\Exceptions\ElementException;
use CraftCms\Cms\Entry\Data\EntryType;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Field\Fields;
use CraftCms\Cms\Field\Matrix;
use CraftCms\Cms\Field\PlainText;
use CraftCms\Cms\FieldLayout\LayoutElements\CustomField;
use CraftCms\Cms\FieldLayout\LayoutElements\TitleField;
use CraftCms\Cms\Http\RespondsWithFlash;
use CraftCms\Cms\Http\Responses\CpScreenResponse;
use CraftCms\Cms\Section\Sections;
use CraftCms\Cms\Twig\TemplateRenderer;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author    studioespresso
 * @package   Seeder
 * @since     1.0.0
 */
class SeederController
{
    use RespondsWithFlash;

    public function __construct(
        private readonly TemplateRenderer $view,
        private readonly Sections $sections,
        private readonly Request $request,
        private readonly Seeder $seeder,
    )
    {
    }

    /**
     * index
     * @author Robin Schambach
     * @since  14.08.2024
     */
    public function index()
    {
        $data = [];
        foreach ($this->sections->getAllSections()->all() as $section) {
            $count = SeederEntryRecord::query()
                ->where('section', $section->id)
                ->count();
            if ($count) {
                $data['sections'][$section->id]['id'] = $section->id;
                $data['sections'][$section->id]['name'] = $section->name;
                $data['sections'][$section->id]['count'] = $count;
            }
        }

        $seededAssets = SeederAssetRecord::query()->count();
        if ($seededAssets) {
            $data['assets']['count'] = $seededAssets;
        }

        $seededUsers = SeederUserRecord::query()->count();
        if ($seededUsers) {
            $data['users']['count'] = $seededUsers;
        }


        return $this->view->renderTemplate('element-seeder/index', ['data' => $data]);
    }

    /**
     * actionClean
     * @author Robin Schambach
     * @since  14.08.2024
     */
    public function clean(Weeder $weeder): \Symfony\Component\HttpFoundation\Response
    {
        $data = $this->request->post('data');
        if ($data) {
            if (!empty($data['sections'])) {
                foreach ($data['sections'] as $sectionId) {
                    $weeder->entries($sectionId);
                }
            }
            if (!empty($data['assets'])) {
                $weeder->assets();
            }
            if (!empty($data['users'])) {
                $weeder->users();
            }
        }

        return $this->asSuccess('successfully deleted', redirect: 'element-seeder-index');
    }

    /**
     * actionElementMatrixModal
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function elementMatrixModal(UniqueFields $uniqueService, Elements $elements)
    {
        $elementId = $this->request->query('elementId');
        $element = $elements->getElementById($elementId);
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

        return (new CpScreenResponse())
            ->contentTemplate('element-seeder/sidebar.twig', [
                'matrixFields' => $matrixFields,
                'elementId'    => $elementId
            ]);
    }

    /**
     * actionElementContentModal
     * @author Robin Schambach
     * @since  08.07.2024
     */
    public function elementContentModal(): CpScreenResponse
    {
        $ids = [];
        $elementId = $this->request->query('elementId');
        if ($elementId) {
            $ids = [$elementId];
        }
        $elementIds = $this->request->query('elementIds');
        if ($elementIds) {
            $ids = $elementIds;
        }

        if (empty($ids)) {
            throw new HttpException(400, 'required ids are missing');
        }


        $handledLayouts = [];

        $layouts = [];
        foreach ($this->getElementsByIds($ids) as $element) {
            $layout = $element->getFieldLayout();
            if (!$layout) {
                continue;
            }

            if (\in_array($layout->id, $handledLayouts, true)) {
                continue;
            }
            $handledLayouts[] = $layout->id;
            $tabData = [];

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
                $tabData[] = $d;
            }
            $layouts[] = $tabData;
        }

        return (new CpScreenResponse())
            ->contentTemplate('element-seeder/generateContent.twig', [
                'elementIds' => $ids,
                'layouts'  => $layouts,
                'action' => 'element-seeder/seeder/generate-content',
            ]);
    }

    /**
     * actionGenerateContent
     *
     * @author Robin Schambach
     * @since  08.07.2024
     */
    public function generateContent(DatabaseManager $db, Elements $elementService)
    {
        $elementIds = $this->request->post('elementIds');
        $elements = $this->getElementsByIds($elementIds);

        $fields = $this->request->post('fields');
        $fieldHandles = [];
        foreach ($fields as $handle => $value) {
            if ((bool) $value) {
                $fieldHandles[] = $handle;
            }
        }

        $db->beginTransaction();
        try {
            foreach ($elements as $element) {
                $this->seeder->populateFields($element, $fieldHandles);
                if (!$elementService->saveElement($element)) {
                    throw new ElementException($element, 'Could not save element due to validation errors');
                }
            }
            $db->commit();
        } catch (\Throwable $throwable) {
            $db->rollBack();
            throw $throwable;
        }

        return $this->asSuccess('Content generated successfully');
    }

    /**
     * actionGenerateMatrix
     *
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public function generateMatrix(UniqueFields $uniqueService, Fields $fields, Elements $elements)
    {
        $elementId = $this->request->post('elementId');
        $element = $elements->getElementById($elementId);

        $uniqueFields = $this->request->post('uniqueFields');
        $i = 1;
        foreach ($uniqueFields as $matrixFieldId => $value) {
            /** @var Matrix $matrixField */
            $matrixField = $fields->getFieldById($matrixFieldId);

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

                            $e = new Entry();
                            $e->setTypeId($entryType->getId());
                            $e->setOwner($element);
                            $e->fieldId = $matrixField->id;

                            $fieldValue['new' . $i] = $this->seeder->getSerializedEntryData($entryType, $e);
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

        if (!$elements->saveElement($element)) {
            return $this->asModelFailure($element);
        }

        return $this->asSuccess('Update erfolgreich', $uniqueFields);
    }

    public function numerizeContentModal()
    {
        $ids = $this->request->query('elementIds');
//        $this->view->registerAssetBundle(SeederStyleBundle::class);
        $handledLayouts = [];
        $allowedFields = [
            PlainText::class
        ];
        $layouts = [];
        foreach ($this->getElementsByIds($ids) as $element) {
            $layout = $element->getFieldLayout();
            if (!$layout) {
                continue;
            }

            if (\in_array($layout->id, $handledLayouts, true)) {
                continue;
            }
            $handledLayouts[] = $layout->id;
            $tabData = [];

            foreach ($layout->getTabs() as $tab) {
                $d = [
                    'tab'    => $tab->name,
                    'fields' => []
                ];
                foreach ($tab->getElements() as $fieldLayoutElement) {
                    if($fieldLayoutElement instanceof TitleField){
                        $d['fields'][] = $fieldLayoutElement;
                        continue;
                    }

                    if(!($fieldLayoutElement instanceof CustomField)){
                        continue;
                    }
                    $realField = $fieldLayoutElement->getField();
                    if(!in_array($realField::class, $allowedFields, true)){
                        continue;
                    }
                    $d['fields'][] = $fieldLayoutElement;
                }

                if(!empty($d['fields'])){
                    $tabData[] = $d;
                }
            }
            $layouts[] = $tabData;
        }

        return (new CpScreenResponse())
            ->contentTemplate('element-seeder/generateContent.twig', [
                'elementIds' => $ids,
                'layouts'  => $layouts,
                'action' => 'element-seeder/seeder/numerize-elements',
            ]);
    }

    public function numerizeElements()
    {
        $elementIds = $this->request->post('elementIds');
        $elements = $this->getElementsByIds($elementIds);

        $fieldConfig = $this->request->post('fields');
        $fields = [];
        foreach ($fieldConfig as $fieldId => $value) {
            if((bool)$value){
                $fields[] = $fieldId;
            }
        }
        $fields = array_unique($fields);

        $this->seeder->numerateElements($elements, $fields);

        return $this->asSuccess('Update erfolgreich');
    }

    /**
     * createUniqueBlocks
     *
     * @param EntryType                $blockType
     * @param array                                  $fields
     * @param UniqueFields $uniqueFields
     * @param                                        $i
     *
     * @return array
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

                $v = $this->seeder->getFieldData($customField);
                if ($v) {
                    $f[$customField->handle] = $v;
                }
            }

            $fieldValue['new' . $i] = [
                'type'   => $blockType->handle,
                'title'  => $blockType->hasTitleField ? app(\Anubarak\Seeder\Seeder\Fields\Fields::class)->Title() : null,
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
     * @return \CraftCms\Cms\Element\Contracts\ElementInterface[]
     * @author Robin Schambach
     * @since  09.07.2024
     */
    protected function getElementsByIds(array $ids): array
    {
        // we always use the same element type
        $class = DB::query()
            ->select(['type'])
            ->from(Table::ELEMENTS)
            ->where('id', '=', $ids[0])
            ->scalar();

        $query = app(Elements::class)->createElementQuery($class);

        return $query->status(null)
            ->id($ids)
            ->fixedOrder()
            ->all();
    }

    //    public function actionTest()
    //    {
    //        /** @var \craft\fields\Link $field */
    //        $field = Craft::$app->getFields()->getFieldByHandle('linkField');
    //
    //
    //        $entry = Entry::findOne(5);
    //        Seeder::$plugin->getSeeder()->populateFields($entry, ['linkField']);
    //        $s = Craft::$app->getElements()->saveElement($entry);
    //        echo "<pre>";
    //        var_dump($s);
    //        echo "</pre>";
    //        die();
    //    }
}
