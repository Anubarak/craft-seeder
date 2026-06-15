<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Seeder;

use Anubarak\Seeder\Seeder\Fields\BaseField;
use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Entry\Data\EntryType;
use CraftCms\Cms\Entry\Elements\Entry;
use Anubarak\Seeder\Events\RegisterFieldTypeEvent;
use Anubarak\Seeder\Models\SeederAssetRecord;
use Anubarak\Seeder\Models\SeederEntryRecord;
use Anubarak\Seeder\Models\SeederUserRecord;
use CraftCms\Cms\Field\Assets;
use CraftCms\Cms\Field\Checkboxes;
use CraftCms\Cms\Field\Color;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Field\Date;
use CraftCms\Cms\Field\Dropdown;
use CraftCms\Cms\Field\Email;
use CraftCms\Cms\Field\Entries;
use CraftCms\Cms\Field\Exceptions\FieldNotFoundException;
use CraftCms\Cms\Field\Lightswitch;
use CraftCms\Cms\Field\Link;
use CraftCms\Cms\Field\LinkTypes\Url;
use CraftCms\Cms\Field\Matrix;
use CraftCms\Cms\Field\Money;
use CraftCms\Cms\Field\MultiSelect;
use CraftCms\Cms\Field\Number;
use CraftCms\Cms\Field\PlainText;
use CraftCms\Cms\Field\RadioButtons;
use CraftCms\Cms\Field\Table;
use CraftCms\Cms\Field\Users;
use CraftCms\Cms\User\Elements\User;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Secondred\Base\Video\Field\VideoInputField;
use Throwable;
use function in_array;

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
#[Singleton]
class Seeder
{
    /**
     * All registered Field Types
     *
     * @var Collection|null $registeredFieldTypes
     */
    protected Collection|null $registeredFieldTypes = null;
    /**
     * @var \Anubarak\Seeder\Seeder\Fields\BaseField[] $fieldInstances
     */
    protected array $fieldInstances = [];
    /**
     * @var \Faker\Generator $factory
     */
    public Generator $factory;

    public function __construct(
        private readonly LogManager      $logger,
        private readonly Fields          $fields,
        private readonly DatabaseManager $db,
        private readonly Elements $elements
    ) {
        $language = app(SeederServiceProvider::class)->getSettings()->fakerProvider;
        $this->factory = Factory::create($language);
    }

    /**
     * @param ElementInterface $element
     * @param array            $fieldHandles
     *
     * @return \CraftCms\Cms\Element\Contracts\ElementInterface
     * @throws \Random\RandomException
     */
    public function populateFields(ElementInterface $element, array $fieldHandles = []): ElementInterface
    {
        $layout = $element->getFieldLayout();
        // no layout -> nothing we can do ¯\_(ツ)_/¯
        if (!$layout) {
            return $element;
        }

        $missRate = SeederServiceProvider::getInstance()->getSettings()->getMissRate();

        $fields = $layout->getCustomFields();
        foreach ($fields as $field) {
            if (!empty($fieldHandles) && !in_array($field->handle, $fieldHandles, true)) {
                continue;
            }

            // skip in case it is not required?
            $fieldElement = $layout->getField($field->handle);
            if ($missRate && !$fieldElement->required && (random_int(1, 100) / 100) < $missRate) {
                continue;
            }

            try {
                $fieldData = $this->getFieldData($field, $element);
                if ($fieldData) {
                    $element->setFieldValue($field->handle, $fieldData);
                }
            } catch (FieldNotFoundException $e) {
                $this->logger->warning('Field not found: ' . $field::class, [__METHOD__]);
            }
        }

        return $element;
    }

    /**
     * @param \CraftCms\Cms\Entry\Elements\Entry $entry
     */
    public function saveSeededEntry(Entry $entry): void
    {
        $record = new SeederEntryRecord();
        $record->entryUid = $entry->uid;
        $record->section = $entry->sectionId;
        $record->save();
    }

    /**
     * @param \CraftCms\Cms\Asset\Elements\Asset $asset
     */
    public function saveSeededAsset(Asset $asset): void
    {
        $record = new SeederAssetRecord();
        $record->assetUid = $asset->uid;
        $record->save();
    }

    /**
     * @param \CraftCms\Cms\User\Elements\User $user
     */
    public function saveSeededUser(User $user): void
    {
        $record = new SeederUserRecord();
        $record->userUid = $user->uid;
        $record->save();
    }

    /**
     * Get all registered field Types
     *
     * @return Collection<int, class-string<BaseField>>
     *
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getRegisteredFieldTypes(): Collection
    {
        if ($this->registeredFieldTypes === null) {
            $event = new RegisterFieldTypeEvent([
                Money::class                                     => \Anubarak\Seeder\Seeder\Fields\Money::class,
                VideoInputField::class                           => \Anubarak\Seeder\Seeder\Fields\VideoInput::class,
                Dropdown::class                                  => \Anubarak\Seeder\Seeder\Fields\Dropdown::class,
                Lightswitch::class                               => \Anubarak\Seeder\Seeder\Fields\Lightswitch::class,
                Date::class                                      => \Anubarak\Seeder\Seeder\Fields\Date::class,
                PlainText::class                                 => \Anubarak\Seeder\Seeder\Fields\PlainText::class,
                Email::class                                     => \Anubarak\Seeder\Seeder\Fields\Email::class,
                Url::class                                       => \Anubarak\Seeder\Seeder\Fields\Url::class,
                Link::class                                      => \Anubarak\Seeder\Seeder\Fields\CraftLink::class,
                Color::class                                     => \Anubarak\Seeder\Seeder\Fields\Color::class,
                Checkboxes::class                                => \Anubarak\Seeder\Seeder\Fields\Checkboxes::class,
                RadioButtons::class                              => \Anubarak\Seeder\Seeder\Fields\RadioButtons::class,
                MultiSelect::class                               => \Anubarak\Seeder\Seeder\Fields\MultiSelect::class,
                Table::class                                     => \Anubarak\Seeder\Seeder\Fields\Table::class,
                Entries::class                                   => \Anubarak\Seeder\Seeder\Fields\Entries::class,
                Assets::class                                    => \Anubarak\Seeder\Seeder\Fields\Assets::class,
                Number::class                                    => \Anubarak\Seeder\Seeder\Fields\Number::class,
                Matrix::class                                    => \Anubarak\Seeder\Seeder\Fields\Matrix::class,
                Users::class                                     => \Anubarak\Seeder\Seeder\Fields\Users::class,
                'verbb\\hyper\\fields\\HyperField'               => \Anubarak\Seeder\Seeder\Fields\Hyper::class,
                'craft\\htmlfield\\HtmlField'                    => \Anubarak\Seeder\Seeder\Fields\Html::class,
                'secondred\\tablemaker\\fields\\TableMakerField' => \Anubarak\Seeder\Seeder\Fields\TableMaker::class,
                'ether\\seo\\fields\\SeoField'                   => \Anubarak\Seeder\Seeder\Fields\Seo::class,
                'verbb\\formie\\fields\\Forms'                   => \Anubarak\Seeder\Seeder\Fields\FormieForm::class
            ]);

            event($event);

            $this->registeredFieldTypes = Collection::make($event->types);
        }

        return $this->registeredFieldTypes;
    }

    /**
     * Get the Field Data
     *
     * @param \CraftCms\Cms\Field\Contracts\FieldInterface          $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface|null $element
     *
     * @return mixed
     * @throws \CraftCms\Cms\Field\Exceptions\FieldNotFoundException
     * @author Robin Schambach
     * @since  05.09.2019
     */
    public function getFieldData(FieldInterface $field, ElementInterface|null $element = null): mixed
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
                    $v = $this->fields->checkForEvent($field, $element);
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
     * @return \Anubarak\Seeder\Seeder\Fields\BaseField
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     * @since  19/12/2023
     * @author Robin Schambach
     */
    public function getFieldInstance(string $class): BaseField
    {
        if (!isset($this->fieldInstances[$class])) {
            $object = app()->make($class, [
                'factory' => $this->factory,
                'fields'  => $this->fields,
            ]);
            $this->fieldInstances[$class] = $object;
        }

        return $this->fieldInstances[$class];
    }

    /**
     * getSerializedEntryData
     *
     * @param EntryType                               $entryType
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface|null $element
     *
     * @return array
     * @author Robin Schambach
     * @since  09.07.2024
     */
    public function getSerializedEntryData(EntryType $entryType, ElementInterface|null $element = null): array
    {
        $fieldValues = [];
        foreach ($entryType->getFieldLayout()?->getCustomFields() as $field) {
            try {
                $value = $this->getFieldData($field, $element);
            } catch (FieldNotFoundException $exception) {
                $value = null;
            }
            if ($value) {
                $fieldValues[$field->handle] = $value;
            }
        }

        return [
            'type'   => $entryType->handle,
            'title'  => $entryType->hasTitleField ? $this->fields->Title() : null,
            'fields' => $fieldValues
        ];
    }

    /**
     * numerateElements
     *
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface[] $elements
     * @param string[]                                           $config
     * @param string                                             $format
     *
     * @return void
     * @throws \Random\RandomException
     * @throws \Throwable
     * @author Robin Schambach
     * @since  14.08.2024
     */
    public function numerateElements(array $elements, array $config, string $format = '{i} - {value}'): void
    {
        $this->db->beginTransaction();
        $locale = App::currentLocale();
        try {
            foreach ($elements as $i => $entry) {
                $hasCustomField = false;
                foreach ($config as $fieldHandle) {
                    if ($fieldHandle === 'title') {
                        $entry->title = \MessageFormatter::formatMessage($locale, $format, [
                            'i'     => $i,
                            'value' => $this->factory->words(random_int(2, 6), true)
                        ]);
                    } else {
                        // custom field
                        $field = $entry->getFieldLayout()->getFieldByHandle($fieldHandle);
                        if (!$field) {
                            continue;
                        }
                        $value = \MessageFormatter::formatMessage($locale, $format, [
                            'i'     => $i,
                            'value' => $this->getFieldData($field, $entry)
                        ]);

                        $entry->setFieldValue($field->handle, $value);
                        $hasCustomField = true;
                    }
                }

                Log::error("save {$entry->id} with {$entry->title}", [__METHOD__]);

                $this->elements->saveElement($entry, false, saveContent: $hasCustomField);
            }
            $this->db->commit();
        } catch (Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }
    }
}
