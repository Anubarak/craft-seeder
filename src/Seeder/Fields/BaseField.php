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

namespace Anubarak\Seeder\Seeder\Fields;

use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Shared\Exceptions\NotSupportedException;
use CraftCms\Cms\User\Elements\User;

/**
 * Class BaseField
 *
 * @package Anubarak\Seeder\Seeder\fields
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
    public function __construct(
        \Faker\Generator $factory,
        protected readonly Fields $fields
    )
    {
        $this->factory = $factory;
    }

    /**
     * getSettings
     *
     * @param \CraftCms\Cms\Field\Contracts\FieldInterface            $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface$element
     *
     * @return array|mixed|null
     * @author Robin Schambach
     * @since  06.09.2019
     */
    public function getSettings(FieldInterface $field, ElementInterface $element)
    {
        $settings = SeederServiceProvider::getInstance()->getSettings()->fieldsConfig;

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
     * @param \CraftCms\Cms\Field\Models\Field            $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface$element
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
        return $this->fields->checkForEvent($field, $element);
    }

    /**
     * run
     *
     * @param \CraftCms\Cms\Field\Contracts\FieldInterface         $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface|null $element
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
     * @param \CraftCms\Cms\Field\Contracts\FieldInterface          $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface|null $element
     *
     * @return mixed
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public abstract function generate(FieldInterface $field, ElementInterface|null $element = null);
}