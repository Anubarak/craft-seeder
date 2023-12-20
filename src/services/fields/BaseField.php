<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2023 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\services\fields;

use anubarak\seeder\Seeder;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\elements\User;
use yii\base\NotSupportedException;

/**
 * Class BaseField
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
abstract class BaseField
{
    /**
     * @var \Faker\Generator
     */
    public \Faker\Generator $factory;

    /**
     * @param \Faker\Generator $factory
     */
    public function __construct(\Faker\Generator $factory)
    {
        $this->factory = $factory;
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
    public function getSettings(FieldInterface $field, ElementInterface $element)
    {
        $settings = Seeder::$plugin->getSettings()->fieldsConfig;

        // TODO refactor
        $index = get_class($element);
        $settingsForElement = null;
        if (isset($settings[$index])) {
            switch ($index) {
                case Entry::class:
                    /** @var Entry $element */
                    $section = $element->getSection();
                    if (isset($settings[$index][$section->handle])) {
                        $settingsForElement = $settings[$index][$section->handle];
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

        if (($settingsForElement !== null) && isset($settingsForElement[$field->handle])) {
            return $settingsForElement[$field->handle];
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
        return Seeder::$plugin->fields->checkForEvent($field, $element);
    }

    /**
     * run
     *
     * @param \craft\base\FieldInterface   $field
     * @param \craft\base\ElementInterface $element
     *
     * @return mixed|string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public function run(FieldInterface $field, ElementInterface $element = null)
    {
        if ($element !== null) {
            $callbackValue = $this->checkForEvent($field, $element);
            if ($callbackValue) {
                return $callbackValue;
            }
        }

        return $this->generate($field, $element);
    }

    /**
     * generate
     *
     * @param \craft\base\FieldInterface        $field
     * @param \craft\base\ElementInterface|null $element
     *
     * @return mixed
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public abstract function generate(FieldInterface $field, ElementInterface $element = null);
}