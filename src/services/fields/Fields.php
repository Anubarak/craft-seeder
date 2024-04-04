<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\services\fields;

use anubarak\seeder\models\ElementConfig;
use anubarak\seeder\models\FieldCallback;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\elements\User;
use anubarak\seeder\Seeder;
use yii\base\NotSupportedException;

/**
 * Fields Service
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Fields extends Component
{
    /**
     * @var \Faker\Generator $factory
     */
    public \Faker\Generator $factory;

    public function __construct()
    {
        parent::__construct();
        $settings = Seeder::$plugin->getSettings()->fakerProvider;
        $this->factory = \Faker\Factory::create($settings);
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
    public function getFieldCallback(Field $field, ElementInterface $element): ?FieldCallback
    {
        $handler = $this->getEventHandler($element);
        if (!$handler) {
            return null;
        }

        foreach ($handler->getFieldConfig() as $config) {
            if ($config->getHandle() === $field->handle) {
                return $config;
            }
        }

        return null;
    }

    /**
     * getEventHandler
     *
     * @param \craft\base\ElementInterface $element
     *
     * @return \anubarak\seeder\models\ElementConfig|null
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function getEventHandler(ElementInterface $element): ?ElementConfig
    {
        $handlers = Seeder::$plugin->getSettings()->getFieldsConfig();
        foreach ($handlers as $handler) {
            if ($handler->match($element)) {
                return $handler;
            }
        }

        return null;
    }

    /**
     * getCallBack
     *
     * @param null $class
     *
     * @return mixed|null
     *
     * @author Robin Schambach
     * @since  06.09.2019
     */
    public function getCallBack($settings, FieldInterface $field, ElementInterface $element, $class = null)
    {
        // just a string, no options, no class
        if (is_string($settings)) {
            $class = $class ?? $this->factory;

            return $class->$settings($field, $element);
        }

        if (is_array($settings) === true) {
            // check if it's a custom class ¯\_(ツ)_/¯

            /// format
            /// [
            ///     [class, 'function'],
            ///     [setting1, setting2]
            /// ]
            if (count($settings) === 2 && is_array($settings[0])) {
                return call_user_func_array($settings[0], $settings[1]);
            }

            /// just a callback
            /// format
            /// [
            ///     [class, 'function']
            /// ]
            if (count($settings) === 2 && is_object($settings[0])) {
                // return call_user_func($settings);
                // PHPstorm says this... need trying ¯\_(ツ)_/¯
                return $settings($field, $element);
            }
        }

        return 'no-value';
    }

    /**
     * Title
     *
     * @param int $maxLength
     *
     * @return string
     *
     * @throws \Exception
     * @since  06.09.2019
     * @author Robin Schambach
     */
    public function Title(int $maxLength = 40): string
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
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  22.06.2021
     */
    public function checkForEvent(FieldInterface $field, ElementInterface $element): mixed
    {
        $fieldCallback = $this->getFieldCallback($field, $element);
        if ($fieldCallback !== null) {
            $value = null;
            if ($fieldCallback->getCallable()) {
                $value = call_user_func($fieldCallback->getCallable(), $this->factory, $field, $element);
            }

            if ($fieldCallback->getFakerMethod()) {
                $value = $this->factory->{$fieldCallback->getFakerMethod()};
            }

            if ($value !== 'no-value') {
                return $value;
            }
        }

        return null;
    }
}