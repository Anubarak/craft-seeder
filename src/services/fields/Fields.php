<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\seeder\services\fields;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\elements\Tag;
use craft\elements\User;
use craft\errors\FieldNotFoundException;
use craft\fields\Assets as AssetsField;
use craft\fields\Categories;
use craft\fields\Checkboxes;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Entries;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\MultiSelect;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Table;
use craft\fields\Tags;
use craft\fields\Url;
use craft\fields\Users;
use craft\helpers\Assets;
use craft\helpers\Db;
use Faker\Factory;
use studioespresso\seeder\Seeder;
use yii\base\NotSupportedException;
use yii\db\Expression;

/**
 * Fields Service
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Fields extends Component
{

    public $factory;

    public function __construct()
    {
        parent::__construct();
        $settings = Seeder::$plugin->getSettings()->fakerProvider;
        $this->factory = Factory::create($settings);

    }

    /**
     * getSettings
     *
     * @param \craft\base\Field            $field
     * @param \craft\base\ElementInterface $element
     *
     * @return array|mixed|null
     *
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  06.09.2019
     */
    public function getSettings(Field $field, ElementInterface $element)
    {
        $settings = Seeder::$plugin->getSettings()->fieldsConfig;

        $index = get_class($element);
        $settingsForElement = null;
        if(isset($settings[$index])){
            switch ($index){
                case Entry::class:
                    /** @var Entry $element */
                    $section = $element->getSection();
                    if(isset($settings[$index][$section->handle])){
                        $settingsForElement = $settings[$index][$section->handle];
                    }
                    break;
                case Category::class:
                    /** @var Category $element */
                    $group = $element->getGroup();
                    if(isset($settings[$index][$group->handle])){
                        $settingsForElement = $settings[$index][$group->handle];
                    }
                    break;
                case User::class:
                    $settingsForElement = $settings[$index];
                    break;
                case Tag::class:
                    throw new NotSupportedException('Creating Tags is not supported via config yet');
                    break;
                case Asset::class:
                    throw new NotSupportedException('Creating Assets is not supported via config yet');
                    break;
            }
        }

        if(($settingsForElement !== null) && isset($settingsForElement[$field->handle])) {
            return $settingsForElement[$field->handle];
        }

        return null;
    }

    /**
     * getCallBack
     *
     * @param        $settings
     * @param null   $class
     *
     * @return mixed|null
     *
     * @author Robin Schambach
     * @since  06.09.2019
     */
    public function getCallBack($settings, $class = null)
    {
        // just a string, no options, no class
        if(is_string($settings)){
            $class = $class ?? $this->factory;
            return call_user_func([$class, $settings]);
        }

        if(is_array($settings) === true){
            // check if it's a custom class ¯\_(ツ)_/¯

            /// format
            /// [
            ///     [class, 'function'],
            ///     [setting1, setting2]
            /// ]
            if(count($settings) === 2 && is_array($settings[0])){
                return call_user_func_array($settings[0], $settings[1]);
            }

            /// just a callback
            /// format
            /// [
            ///     [class, 'function']
            /// ]
            if(count($settings) === 2 && is_object($settings[0])){
                // return call_user_func($settings);
                // PHPstorm says this... need trying ¯\_(ツ)_/¯
                return $settings();
            }
        }

        return 'no-value';
    }

    /**
     * Title
     *
     * @param int $maxLength
     *
     * @return bool|string
     *
     * @author Robin Schambach
     * @since  06.09.2019
     * @throws \Exception
     */
    public function Title($maxLength = 40)
    {
        $title = $this->factory->text(random_int(8, $maxLength));
        $title = substr($title, 0, strlen($title) - 1);
        return $title;
    }

    /**
     * checkForEvent
     *
     * @param \craft\base\Field            $field
     * @param \craft\base\ElementInterface $element
     *
     * @return mixed|string|null
     *
     * @author Robin Schambach
     * @since  22.06.2021
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\InvalidConfigException
     */
    public function checkForEvent(Field $field, ElementInterface $element)
    {
        $settings = $this->getSettings($field, $element);
        if($settings !== null){
            if(is_callable($settings) === true){
                return $settings($field, $element);
            }

            $value = $this->getCallBack($settings);

            if($value !== 'no-value'){
                return $value;
            }
        }

        return null;
    }

    /**
     * @param PlainText $field
     * @param Entry     $entry
     *
     * @return mixed|null
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\InvalidConfigException
     */
    public function PlainText($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        return $this->factory->realText($field->charLimit ? $field->charLimit : 200);
    }

    /**
     * @param Email $field
     *
     * @return
     */
    public function Email($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }
        return $this->factory->email();
    }

    /**
     * @param Url $field
     **
     *
     * @return
     */
    public function Url($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }
        return $this->factory->url();
    }

    public function Number(Number $field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }
        $min = $field->min ?? 0;
        $max = $field->max ?? 100;

        return random_int($min, $max);
    }

    public function Color($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }
        return $this->factory->safeHexColor;

    }

    public function Date($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }
        return $this->factory->dateTime();
    }

    /**
     * @param Categories $field
     * @param Entry      $entry
     *
     * @return array
     * @throws \Exception
     */
    public function Categories($field, $entry): array
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        $source = $field->source;
        $groupId = null;
        if($source){
            $groupUid = str_replace('group:', '', $source);
            $groupId = Db::idByUid(\craft\db\Table::CATEGORYGROUPS, $groupUid);
        }

        $cats = Category::find()
            ->groupId($groupId)
            ->limit(random_int(1, 2))
            ->orderBy(new Expression('rand()'))
            ->ids();

        return $cats;
    }

    /**
     * @param Dropdown $field
     * @param Entry    $entry
     *
     * @return
     */
    public function Dropdown($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        return $field->options[array_rand($field->options)]['value'];
    }

    /**
     * @param Checkboxes $field
     * @param Entry      $entry
     *
     * @return array
     * @throws \Exception
     */
    public function Checkboxes($field, $entry): array
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        $checkedBoxes = [];
        for ($x = 1, $xMax = random_int(1, count($field->options)); $x <= $xMax; $x++) {
            $checkedBoxes[] = $field->options[array_rand($field->options)]['value'];
        }
        return $checkedBoxes;
    }

    /**
     * @param RadioButtons $field
     * @param Entry        $entry
     *
     * @return
     */
    public function RadioButtons($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        return $field->options[array_rand($field->options)]['value'];
    }

    /**
     * @param MultiSelect $field
     * @param Entry       $entry
     *
     * @return array
     * @throws \Exception
     */
    public function MultiSelect($field, $entry): array
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        $options = [];
        for ($x = 1, $xMax = random_int(1, count($field->options)); $x <= $xMax; $x++) {
            $options[] = $field->options[array_rand($field->options)]['value'];
        }
        return $options;
    }

    /**
     * @param Lightswitch $field
     * @param Entry       $entry
     *
     * @return bool
     */
    public function Lightswitch($field, $entry): bool
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        return $this->factory->boolean;
    }

    /**
     * @param Table $field
     * @param Entry $entry
     *
     * @return array
     * @throws \Exception
     */
    public function Table($field, $entry): array
    {

        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        if ($field->minRows) {
            $min = $field->minRows;
        } else {
            $min = 1;
        }
        if ($field->maxRows) {
            $max = $field->maxRows;
        } else {
            $max = $min + 10;
        }

        $table = [];
        for ($x = 0, $xMax = random_int($min, $max); $x <= $xMax; $x++) {
            foreach ($field->columns as $handle => $col) {
                switch ($col['type']) {
                    case 'singleline':
                        $table[$x][$handle] = $this->factory->text(30);
                        break;
                    case 'multiline':
                        $table[$x][$handle] = $this->factory->realText(150, random_int(2, 5));
                        break;
                    case 'lightswitch':
                        $table[$x][$handle] = $this->factory->boolean;
                        break;
                    case 'number':
                        $table[$x][$handle] = $this->factory->numberBetween(2, 30);
                        break;
                    case 'checkbox':
                        $table[$x][$handle] = $this->factory->boolean;
                        break;
                    case 'date':
                        $table[$x][$handle] = $this->factory->dateTime;
                        break;
                    case 'time':
                        $table[$x][$handle] = $this->factory->dateTime;
                        break;
                    case 'color':
                        $table[$x][$handle] = $this->factory->hexColor;
                        break;
                }
            }
        }
        return $table;
    }

    /**
     * @param Tags  $field
     * @param Entry $entry
     *
     * @return array
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function Tags($field, $entry): array
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        return Tag::find()->limit(random_int(1, 5))->orderBy('rand()')->ids();
    }

    /**
     * @param Users $field
     * @param Entry $entry
     *
     * @throws \craft\errors\FieldNotFoundException
     */
    public function Users($field, $entry): void
    {
        throw new FieldNotFoundException('Users field not supported');
    }

    /**
     * Assets
     *
     * @param \craft\fields\Assets $field
     *
     * @return array
     *
     * @author Robin Schambach
     * @since  04.12.2020
     * @throws \Exception
     */
    public function Assets(\craft\fields\Assets $field, ElementInterface $element): array
    {
        $callbackValue = $this->checkForEvent($field, $element);
        if($callbackValue){
            return $callbackValue;
        }

        $source = $field->sources;
        $volumeIds = [];
        if($source !== '*'){

            if(!is_array($source)){
                $source = [$source];
            }

            foreach ($source as $s){
                $volumeUid = str_replace('folder:', '', $s);
                $volumeIds[] = Db::idByUid(\craft\db\Table::VOLUMES, $volumeUid);
            }

        }

        $limit = 2;
        if($field->limit){
            $limit = $field->limit;
        }


        $query = Asset::find()
            ->limit(random_int(1, $limit))
            ->orderBy(new Expression('rand()'));
        if($volumeIds){
            $query->volumeId($volumeIds);
        }

        if($field->allowedKinds){
            $query->kind($field->allowedKinds);
        }

        $assetIds = $query->ids();

        return $assetIds;
    }

    /**
     * @param Entries $field
     *
     * @param         $entry
     *
     * @return array|int[]
     * @throws \Exception
     */
    public function Entries(Entries $field, $entry): array
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

        $sources = $field->sources;
        $sectionIds = [];
        foreach ($sources as $source) {
            $sectionUid = str_replace('section:', '', $source);
            $sectionIds[] = Db::idByUid(\craft\db\Table::SECTIONS, $sectionUid);
        }

        $limit = 2;
        if($field->limit){
            $limit = $field->limit;
        }

        $query = Entry::find()
            ->sectionId($sectionIds)
            ->limit(random_int(1, $limit))
            ->orderBy(new Expression('rand()'));

        if($field->targetSiteId){
            $site = Craft::$app->getSites()->getSiteByUid($field->targetSiteId);
            $query->siteId($site->id);
        }elseif ($entry->siteId){
            $query->siteId($entry->siteId);
        }

        $entryIds = $query->ids();

        return $entryIds;
    }

    /**
     * @param Matrix $field
     * @param        $entry
     *
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\ExitException
     */
    public function Matrix($field, $entry)
    {
        $callbackValue = $this->checkForEvent($field, $entry);
        if($callbackValue){
            return $callbackValue;
        }

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
            $blockTypeFields = Craft::$app->fields->getFieldsByLayoutId($type->fieldLayoutId);
            $matrixBlock = new MatrixBlock();
            $matrixBlock->typeId = $type->id;
            $matrixBlock->fieldId = $field->id;
            $matrixBlock->ownerId = $entry->id;
            Craft::$app->elements->saveElement($matrixBlock);
            $matrixBlock = Seeder::$plugin->seeder->populateFields($blockTypeFields, $matrixBlock);
            Craft::$app->elements->saveElement($matrixBlock);
        }
        return;
    }

    private function uploadNewAsset($folderId, $path): Asset
    {
        $assets = Craft::$app->getAssets();
        $folder = $assets->findFolder(['id' => $folderId]);

        if (!$folder) {
            throw new BadRequestHttpException('The target folder provided for uploading is not valid');
        }

        // Check the permissions to upload in the resolved folder.
        $filename = Assets::prepareAssetName($path);

        $asset = new Asset();
        $asset->tempFilePath = $path;
        $asset->filename = $filename;
        $asset->newFolderId = $folder->id;
        $asset->volumeId = $folder->volumeId;
        $asset->avoidFilenameConflicts = true;
        $asset->setScenario(Asset::SCENARIO_CREATE);

        $result = Craft::$app->getElements()->saveElement($asset);

        return $asset;
    }
}