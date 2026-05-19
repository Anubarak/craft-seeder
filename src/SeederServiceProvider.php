<?php


namespace Anubarak\Seeder;

use Anubarak\Seeder\Commands\CleanUp;
use Anubarak\Seeder\Commands\Generate;
use Anubarak\Seeder\Commands\Populate;
use Anubarak\Seeder\Listeners\MetaFieldHtml;
use Anubarak\Seeder\Listeners\RegisterElementActions;
use Craft;
use CraftCms\Cms\Element\Events\ElementActionsResolving;
use CraftCms\Cms\Element\Events\ElementMetaFieldsHtmlResolving;
use CraftCms\Cms\Plugin\Plugin;

/**
 * @package   Seeder
 * @method    Settings getSettings()
 */
class SeederServiceProvider extends Plugin
{
    /**
     * @inheritdoc
     */
    public string $schemaVersion = '1.0.0';
    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = false;
    /**
     * @inheritdoc
     */
    public bool     $hasCpSection = true;
    public array    $events       = [
        ElementActionsResolving::class        => RegisterElementActions::class,
        ElementMetaFieldsHtmlResolving::class => MetaFieldHtml::class
    ];
    protected array $scripts      = [
        __DIR__ . '/../resources/js/seeder.js' => 'js/seeder.js',
    ];
    protected array $styles       = [
        __DIR__ . '/../resources/css/seeder.css' => 'css/seeder.css',
    ];


    protected array $commands = [
        CleanUp::class,
        Generate::class,
    ];
    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     * @return Settings
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
