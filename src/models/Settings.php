<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\models;

use craft\config\BaseConfig;

/**
 * Seeder Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Settings extends BaseConfig
{
    public $debug           = false;
    public $eachMatrixBlock = false;
    /**
     * Config to provide additional information/options for certain fields in certain layouts/ scenarios
     * it's indexed by element type
     *
     *  'fieldsConfig' => [
     *      new EntryConfig('news', [
     *          (new FieldCallback('headline'))
     *              ->setCallable(fn() => 'foooooooo'),
     *
     *          (new FieldCallback('textOverview'))
     *              ->setCallable(fn(\Faker\Generator $faker) => 'bar ' .  $faker->text())
     *      ])
     *  ]
     *
     *
     * @var array $fieldsConfig
     */
    public $fieldsConfig = [];
    /**
     * The language it should use
     *
     * @var string $fakerProvider
     */
    public $fakerProvider = 'de_DE';

    /**
     * fieldsConfig
     *
     * @param \anubarak\seeder\models\ElementConfig[] $config
     *
     * @return $this
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function fieldsConfig(array $config)
    {
        $this->fieldsConfig = $config;

        return $this;
    }

    /**
     * setFieldsConfig
     *
     * @param array $config
     *
     * @return $this
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function setFieldsConfig(array $config)
    {
        $this->fieldsConfig = $config;

        return $this;
    }

    /**
     * getFieldsConfig
     *
     * @return \anubarak\seeder\models\ElementConfig[]
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function getFieldsConfig(): array
    {
        return $this->fieldsConfig;
    }

    /**
     * @inheritdoc
     */
    public function extraFields(): array
    {
        $attr =  parent::extraFields();
        $attr[] = 'fieldsConfig';

        return $attr;
    }
}
