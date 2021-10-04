<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\seeder\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\errors\FieldNotFoundException;
use studioespresso\seeder\events\RegisterFieldTypeEvent;
use studioespresso\seeder\records\SeederAssetRecord;
use studioespresso\seeder\records\SeederCategoryRecord;
use studioespresso\seeder\records\SeederEntryRecord;
use studioespresso\seeder\records\SeederUserRecord;
use studioespresso\seeder\Seeder;

/**
 * SeederService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 *
 * @property void $registeredFieldTypes
 */
class SeederService extends Component
{
    public const REGISTER_FIELD_TYPES = 'registerFieldTypes';
    /**
     * All registered Field Types
     *
     * @var array $_registeredFieldTypes
     */
    private $_registeredFieldTypes;

    /**
     * @param                  $fields
     * @param ElementInterface $element
     *
     * @return \craft\base\ElementInterface
     * @throws \yii\base\ExitException
     */
    public function populateFields($fields, ElementInterface $element): ElementInterface
    {
        foreach ($fields as $field) {
            try {
                $fieldData = $this->getFieldData($field, $element);
                if ($fieldData) {
                    $element->setFieldValue($field->handle, $fieldData);
                   }
            } catch (FieldNotFoundException $e) {
                if (Seeder::$plugin->getSettings()->debug) {
                    Craft::dd($e);
                } else {
                    echo 'Fieldtype not supported:' . get_class($field) . "\n";
                }
            }
        }

        return $element;
    }

    /**
     * @param \craft\elements\Entry $entry
     */
    public function saveSeededEntry($entry): void
    {
        $record = new SeederEntryRecord();
        $record->entryUid = $entry->uid;
        $record->section = $entry->sectionId;
        $record->save();
    }

    /**
     * @param \craft\elements\Asset $asset
     */
    public function saveSeededAsset($asset): void
    {
        $record = new SeederAssetRecord();
        $record->assetUid = $asset->uid;
        $record->save();
    }

    /**
     * @param \craft\elements\User $user
     */
    public function saveSeededUser($user): void
    {
        $record = new SeederUserRecord();
        $record->userUid = $user->uid;
        $record->save();
    }

    /**
     * @param \craft\elements\Category $category
     */
    public function saveSeededCategory($category): void
    {
        $record = new SeederCategoryRecord();
        $record->section = $category->groupId;
        $record->categoryUid = $category->uid;
        $record->save();
    }

    /**
     * Get all registered field Types
     *
     * @return array
     *
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getRegisteredFieldTypes(): array
    {
        if ($this->_registeredFieldTypes === null) {
            $event = new RegisterFieldTypeEvent();
            if ($this->hasEventHandlers(self::REGISTER_FIELD_TYPES)) {
                $this->trigger(self::REGISTER_FIELD_TYPES, $event);
            }

            $this->_registeredFieldTypes = $event->types;
        }

        return $this->_registeredFieldTypes;
    }

    /**
     * Get the Field Data
     *
     * @param                              $field
     *
     * @param \craft\base\ElementInterface $element
     *
     * @return mixed
     *
     * @throws \craft\errors\FieldNotFoundException
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getFieldData($field, ElementInterface $element)
    {
        $class = get_class($field);
        $registeredFieldTypes = $this->getRegisteredFieldTypes();

        if (isset($registeredFieldTypes[$class])) {
            return call_user_func($registeredFieldTypes[$class], $field, $element);
        }

        // last chance, try to find a valid callback
        foreach ($registeredFieldTypes as $fieldType){

            if(is_string($fieldType) && $class === $fieldType){
                $v = Seeder::$plugin->fields->checkForEvent($field, $element);
                if($v){
                    return $v;
                }
            }
        }

        throw new FieldNotFoundException('the field ' . $class . ' could not be found');
    }
}
