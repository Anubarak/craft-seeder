<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\services;

use anubarak\seeder\services\fields\BaseField;
use anubarak\seeder\services\fields\Html;
use anubarak\seeder\services\fields\Seo;
use anubarak\seeder\services\fields\TableMaker;
use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\errors\FieldNotFoundException;
use anubarak\seeder\events\RegisterFieldTypeEvent;
use anubarak\seeder\records\SeederAssetRecord;
use anubarak\seeder\records\SeederEntryRecord;
use anubarak\seeder\records\SeederUserRecord;
use anubarak\seeder\Seeder;
use craft\fields\Assets;
use craft\fields\Checkboxes;
use craft\fields\Color;
use craft\fields\Date;
use craft\fields\Dropdown;
use craft\fields\Email;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\MultiSelect;
use craft\fields\Number;
use craft\fields\PlainText;
use craft\fields\RadioButtons;
use craft\fields\Table;
use craft\fields\Url;
use Faker\Generator;
use Illuminate\Support\Collection;

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
     * @var Collection|null $registeredFieldTypes
     */
    protected Collection|null $registeredFieldTypes = null;
    /**
     * @var \anubarak\seeder\services\fields\BaseField[] $fieldInstances
     */
    protected array $fieldInstances = [];
    /**
     * @var \Faker\Generator $factory
     */
    public Generator $factory;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $language = Seeder::$plugin->getSettings()->fakerProvider;
        $this->factory = \Faker\Factory::create($language);

        parent::__construct($config);
    }

    /**
     * @param ElementInterface $element
     *
     * @return \craft\base\ElementInterface
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     */
    public function populateFields(ElementInterface $element): ElementInterface
    {
        $layout = $element->getFieldLayout();
        // no layout -> nothing we can do ¯\_(ツ)_/¯
        if (!$layout) {
            return $element;
        }

        $fields = $layout->getCustomFields();

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
    public function saveSeededEntry(Entry $entry): void
    {
        $record = new SeederEntryRecord();
        $record->entryUid = $entry->uid;
        $record->section = $entry->sectionId;
        $record->save();
    }

    /**
     * @param \craft\elements\Asset $asset
     */
    public function saveSeededAsset(Asset $asset): void
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
     * Get all registered field Types
     *
     * @return array
     *
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getRegisteredFieldTypes(): Collection
    {
        if ($this->registeredFieldTypes === null) {
            $event = new RegisterFieldTypeEvent([
                'types' => [
                    Dropdown::class                                  => \anubarak\seeder\services\fields\Dropdown::class,
                    Lightswitch::class                               => \anubarak\seeder\services\fields\Lightswitch::class,
                    Date::class                                      => \anubarak\seeder\services\fields\Date::class,
                    PlainText::class                                 => \anubarak\seeder\services\fields\PlainText::class,
                    Email::class                                     => \anubarak\seeder\services\fields\Email::class,
                    Url::class                                       => \anubarak\seeder\services\fields\Url::class,
                    Color::class                                     => \anubarak\seeder\services\fields\Color::class,
                    Checkboxes::class                                => \anubarak\seeder\services\fields\Checkboxes::class,
                    RadioButtons::class                              => \anubarak\seeder\services\fields\RadioButtons::class,
                    MultiSelect::class                               => \anubarak\seeder\services\fields\MultiSelect::class,
                    Table::class                                     => \anubarak\seeder\services\fields\Table::class,
                    \craft\fields\Entries::class                     => \anubarak\seeder\services\fields\Entries::class,
                    Assets::class                                    => \anubarak\seeder\services\fields\Assets::class,
                    Number::class                                    => \anubarak\seeder\services\fields\Number::class,
                    Matrix::class                                    => \anubarak\seeder\services\fields\Matrix::class,
                    \craft\fields\Tags::class                        => \anubarak\seeder\services\fields\Tags::class,
                    'verbb\\hyper\\fields\\HyperField'               => \anubarak\seeder\services\fields\Hyper::class,
                    'craft\\htmlfield\\HtmlField'                    => Html::class,
                    'secondred\\tablemaker\\fields\\TableMakerField' => TableMaker::class,
                    'ether\\seo\\fields\\SeoField'                   => Seo::class
                ]
            ]);
            if ($this->hasEventHandlers(self::REGISTER_FIELD_TYPES)) {
                $this->trigger(self::REGISTER_FIELD_TYPES, $event);
            }

            $this->registeredFieldTypes = Collection::make($event->types);
        }

        return $this->registeredFieldTypes;
    }

    /**
     * Get the Field Data
     *
     * @param \craft\base\FieldInterface        $field
     * @param \craft\base\ElementInterface|null $element
     *
     * @return mixed
     *
     * @throws \craft\errors\FieldNotFoundException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getFieldData(FieldInterface $field, ElementInterface $element = null): mixed
    {
        $class = get_class($field);
        $registeredFieldTypes = $this->getRegisteredFieldTypes();

        $typeClass = $registeredFieldTypes->firstWhere(fn($class, $type) => is_a($field, $type));
        if ($typeClass) {
            $fieldClass = $this->getFieldInstance($typeClass);

            return $fieldClass->run($field, $element);
        }

        if ($element !== null) {
            // last chance, try to find a valid callback
            foreach ($registeredFieldTypes as $fieldType) {
                if (is_string($fieldType) && is_a($field, $fieldType)) {
                    $v = Seeder::$plugin->fields->checkForEvent($field, $element);
                    if ($v) {
                        return $v;
                    }
                }
            }
        }

        throw new FieldNotFoundException($field->uid, 'the field ' . $class . ' could not be found');
    }

    /**
     * getFieldInstance
     *
     * @param string $class
     *
     * @return \anubarak\seeder\services\fields\BaseField
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public function getFieldInstance(string $class): BaseField
    {
        if (!isset($this->fieldInstances[$class])) {
            $object = Craft::createObject([
                'class'         => $class,
                '__construct()' => [
                    'factory' => $this->factory
                ]
            ]);
            $this->fieldInstances[$class] = $object;
        }

        return $this->fieldInstances[$class];
    }
}
