<?php


namespace anubarak\seeder;

use anubarak\seeder\models\Settings;
use anubarak\seeder\services\Categories;
use anubarak\seeder\services\Entries;
use anubarak\seeder\services\fields\CkEditor;
use anubarak\seeder\services\fields\CTA;
use anubarak\seeder\services\fields\Fields;
use anubarak\seeder\services\fields\Redactor;
use anubarak\seeder\services\fields\Supertable;
use anubarak\seeder\services\SeederService;
use anubarak\seeder\services\Entries as EntriesService;
use anubarak\seeder\services\Categories as CategoriesService;
use anubarak\seeder\services\Users;
use anubarak\seeder\services\Weeder as WeederService;
use anubarak\seeder\services\Users as UsersService;
use anubarak\seeder\services\fields\Fields as FieldsService;
use anubarak\seeder\services\fields\Redactor as RedactorService;
use anubarak\seeder\services\fields\CkEditor as CkEditorService;
use anubarak\seeder\services\fields\Supertable as SupertableService;
use anubarak\seeder\services\fields\CTA as CTAService;
use anubarak\seeder\services\fields\Positionfieldtype as PositionService;
use craft\base\Plugin;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 *
 * @package   Seeder
 * @since     1.0.0
 * @property  SeederService seeder
 * @property  WeederService weeder
 * @property  EntriesService entries
 * @property  CategoriesService categories
 * @property  UsersService users
 * @property  FieldsService fields
 * @property  RedactorService redactor
 * @property  CkEditorService ckeditor
 * @property  SupertableService supertable
 * @property  CTAService cta
 * @property  PositionService positionfieldtype
 * @property  Settings $settings
 * @method    Settings getSettings()
 */
class Seeder extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Seeder::$plugin
     *
     * @var Seeder
     */
    public static Seeder $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';
    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = false;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = true;

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();

        self::$plugin = $this;

        $this->components = [
            'seeder'            => SeederService::class,
            'weeder'            => WeederService::class,
            'entries'           => Entries::class,
            'users'             => Users::class,
            'fields'            => Fields::class,
            'redactor'          => Redactor::class,
            'ckeditor'          => CkEditor::class,
            'supertable'        => Supertable::class,
            'positionfieldtype' => PositionService::class,
        ];

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            static function (RegisterUrlRulesEvent $event) {
                $event->rules['seeder'] = 'element-seeder/seeder/index';
            }
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return SeederService|object
     */
    public function getSeeder(): SeederService
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->get('seeder');
    }
}
