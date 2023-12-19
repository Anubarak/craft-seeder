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
    public function getSettings(Field $field, ElementInterface $element)
    {
        $settings = Seeder::$plugin->getSettings()->fieldsConfig;

        $index = get_class($element);
        $settingsForElement = null;
        if(isset($settings[$index])){
            switch ($index){
                case Entry::class:
                    /** @var Entry $element */
                    $section = $element->getSection();
                    if(isset($settings[$index][$section->handle])){
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

        if(($settingsForElement !== null) && isset($settingsForElement[$field->handle])) {
            return $settingsForElement[$field->handle];
        }

        return null;
    }

    /**
     * getCallBack
     *
     * @param null   $class
     *
     * @return mixed|null
     *
     * @author Robin Schambach
     * @since  06.09.2019
     */
    public function getCallBack($settings, FieldInterface $field, ElementInterface $element, $class = null)
    {
        // just a string, no options, no class
        if(is_string($settings)){
            $class = $class ?? $this->factory;
            return $class->$settings($field, $element);
        }

        if(is_array($settings) === true){
            // check if it's a custom class ¯\_(ツ)_/¯

            /// format
            /// [
            ///     [class, 'function'],
            ///     [setting1, setting2]
            /// ]
            if(count($settings) === 2 && is_array($settings[0])){
                return call_user_func_array($settings[0], $settings[1]);
            }

            /// just a callback
            /// format
            /// [
            ///     [class, 'function']
            /// ]
            if(count($settings) === 2 && is_object($settings[0])){
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
     * @return bool|string
     *
     * @author Robin Schambach
     * @since  06.09.2019
     * @throws \Exception
     */
    public function Title($maxLength = 40)
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
     * @author Robin Schambach
     * @since  22.06.2021
     * @throws \yii\base\NotSupportedException
     * @throws \yii\base\InvalidConfigException
     */
    public function checkForEvent(FieldInterface $field, ElementInterface $element): mixed
    {
        $settings = $this->getSettings($field, $element);
        if($settings !== null){
            if(is_callable($settings) === true){
                return $settings($field, $element);
            }

            $value = $this->getCallBack($settings, $field, $element);

            if($value !== 'no-value'){
                return $value;
            }
        }

        return null;
    }
}