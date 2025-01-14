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
    public bool $debug           = false;
    public bool $eachMatrixBlock = false;
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
    public array $fieldsConfig = [];
    /**
     * The language it should use
     *
     * @var string $fakerProvider
     */
    public string $fakerProvider = 'de_DE';
    /**
     * chance that a not required field ends up empty
     *
     * @var ?float $missRate
     */
    public ?float $missRate = 0.20;





    /**
     * should certain fields assume the content by it's handle?
     * For example: firstName -> should be a first name field and is populated by a first name
     *
     * @var bool $guessFieldContentByHandle
     */
    public bool $guessFieldContentByHandle = true;

    /**
     * fieldsConfig
     *
     * @param \anubarak\seeder\models\ElementConfig[] $config
     *
     * @return $this
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function fieldsConfig(array $config): self
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
    public function setFieldsConfig(array $config): self
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

    /**
     * @return ?float
     */
    public function getMissRate(): ?float
    {
        return $this->missRate;
    }

    /**
     * @param ?float $missRate
     *
     * @return Settings
     */
    public function missRate(?float $missRate): Settings
    {
        $this->missRate = $missRate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isGuessFieldContentByHandle(): bool
    {
        return $this->guessFieldContentByHandle;
    }

    /**
     * @param bool $guessFieldContentByHandle
     *
     * @return Settings
     */
    public function guessFieldContentByHandle(bool $guessFieldContentByHandle): Settings
    {
        $this->guessFieldContentByHandle = $guessFieldContentByHandle;

        return $this;
    }
}
