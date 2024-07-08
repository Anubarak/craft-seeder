<?php


namespace anubarak\seeder;

use anubarak\seeder\models\Settings;
use anubarak\seeder\services\Assets;
use anubarak\seeder\services\Entries;
use anubarak\seeder\services\fields\Fields;
use anubarak\seeder\services\SeederService;
use anubarak\seeder\services\Entries as EntriesService;
use anubarak\seeder\services\Users;
use anubarak\seeder\services\Weeder as WeederService;
use anubarak\seeder\services\Users as UsersService;
use anubarak\seeder\services\fields\Fields as FieldsService;
use anubarak\seeder\web\assets\cp\SeederAssetBundle;
use craft\base\Element;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\fields\Matrix;
use craft\helpers\Html;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use yii\base\Event;

/**
 *
 * @package   Seeder
 * @since     1.0.0
 * @property  SeederService     seeder
 * @property  WeederService     weeder
 * @property  EntriesService    entries
 * @property  UsersService      users
 * @property  FieldsService     fields
 * @property  Settings          $settings
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

    /**
     * init
     *
     * @return void
     * @author Robin Schambach
     * @since  26.06.2024
     */
    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->components = [
            'seeder'     => SeederService::class,
            'weeder'     => WeederService::class,
            'entries'    => Entries::class,
            'users'      => Users::class,
            'fields'     => Fields::class,
            'assets'     => Assets::class,
        ];

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            static function(RegisterUrlRulesEvent $event) {
                $event->rules['element-seeder'] = 'element-seeder/seeder/index';
            }
        );

        Event::on(
            Element::class,
            Element::EVENT_DEFINE_META_FIELDS_HTML,
            static function(DefineHtmlEvent $event) {
                if (!\Craft::$app->getConfig()->getGeneral()->devMode) {
                    return;
                }
                if (!\Craft::$app->getUser()->getIsAdmin()) {
                    return;
                }
                if (!$event->sender->id) {
                    return;
                }

                if (!$event->sender instanceof Entry) {
                    return;
                }

                /** @var \craft\base\ElementInterface $element */
                $element = $event->sender;
                $show = false;
                $customFields = $element->getFieldLayout()?->getCustomFields() ?? [];
                foreach ($customFields as $field) {
                    if ($field instanceof Matrix) {
                        $show = true;
                        break;
                    }
                }


                \Craft::$app->getView()->registerAssetBundle(SeederAssetBundle::class);


                $div = Html::tag('div', '', [
                    'data-icon' => 'wand-magic-sparkles'
                ]);
                $content = Html::tag('button', $div. 'Seed Content', [
                    'type' => 'button',
                    'data' => [
                        'element-id' => $event->sender->id
                    ],
                    'class' => [
                        'btn',
                        'seed-element-content',
                    ]
                ]);

                if ($show) {
                    $div = Html::tag('div', '', [
                        'data-icon' => 'wand-magic-sparkles'
                    ]);
                    $content .= Html::tag('button', $div. 'Seed Matrix', [
                        'type' => 'button',

                        'data' => [
                            'element-id' => $event->sender->id
                        ],
                        'class' => [
                            'btn',
                            'seed-element',
                        ]
                    ]);
                }

                $outerDiv = Html::tag('div', $content , [
                    'class' => [
                        'flex'
                    ]
                ]);
                $event->html = $outerDiv;

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
     * @return SeederService
     */
    public function getSeeder(): SeederService
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->get('seeder');
    }
}
