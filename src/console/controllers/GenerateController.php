<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\seeder\console\controllers;

use craft\elements\User;
use craft\errors\FieldNotFoundException;
use craft\helpers\Json;
use secondred\base\fields\IncrementField;
use studioespresso\seeder\Seeder;

use Craft;
use studioespresso\seeder\services\Seeder_EntriesService;
use studioespresso\seeder\services\SeederService;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Seeder for Craft CMS 3.x - by Studio Espresso
 *
 * This plugin allows you to quickly create dummy data that you can use while building your site.
 * Issues or feedback: https://github.com/studioespresso/craft3-seeder/issues
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class GenerateController extends Controller
{

    /**
     * Section handle or id
     * @var String
     */
    public $section;

    /**
     * Categories or user group handle or id
     * @var String
     */
    public $group;

    /**
     * Number of entries to be seeded
     * @var Integer
     */
    public $count = 20;

    public $site = null;

    // Public Methods
    // =========================================================================


    public function options($actionId){
        switch ($actionId) {
            case 'entries':
                return ['section','count', 'site'];
            case 'categories':
                return ['group','count', 'site'];
            case 'users':
                return ['group','count'];
        }
    }

    /**
     * Generates entries for the specified section
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionEntries()
    {
        if(!$this->section) {
            echo "Section handle or id missing, please specify\n";
            return;
        }

        $result = Seeder::$plugin->entries->generate($this->section, $this->count, $this->site);
        return $result;
    }

    /**
     * Generates categories for the specified group
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionCategories()
	{

        if(!$this->group) {
            echo "Group handle or id missing, please specify\n";
            return ExitCode::CONFIG;
        }
		$result = Seeder::$plugin->categories->generate($this->group, $this->count);

		return ExitCode::OK;
	}

    /**
     * Generates users for the specified usergroup
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function actionUsers()
    {
        if (Craft::$app->getEdition() !== Craft::Pro) {
            echo "Users requires your Craft install to be upgrade to Pro. You can trial Craft Pro in the control panel\n";
            return ExitCode::CONFIG;
        }

//        $user = new User();
//        $fields = $user->getFieldLayout()->getFields();
//        foreach ($fields as $field){
//            try{
//                $data = Seeder::$plugin->getSeeder()->getFieldData($field, $user);
//
//                if(is_string($data)){
//                    $message = $data;
//                }elseif (is_array($data)){
//                    $message = Json::encode($data);
//                }else{
//                    try{
//                        $message = (string)$data;
//                    }catch (\Exception $exception){
//                        $message = '';
//                    }
//                }
//
//                $this->stdout($field->handle . $message . PHP_EOL);
//            }catch (FieldNotFoundException $exception){
//                $this->stdout($field->handle . ' could not be found' . PHP_EOL);
//            }
//        }
//        Craft::$app->getElements()->saveElement($user);

        $result = Seeder::$plugin->users->generate($this->group, $this->count);
        return ExitCode::OK;
    }

    /**
     * Generates a set of elements predefined in your config/seeder.php
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @param string $name
     *
     * @return mixed
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function actionSet($name = 'default') {
        if(!array_key_exists($name, Seeder::$plugin->getSettings()->sets)) {
            echo "Set not found\n";
            return;
        }
        $settings = Seeder::$plugin->getSettings()->sets[$name];
        foreach($settings as $type => $option) {
            d($type, $option);
            switch ($type) {
                case 'Users':
                    if(is_array($option)) {
                        foreach ($option as $group => $count) {
                            $result = Seeder::$plugin->users->generate($group, $count);
                            if($result) {
                                echo "Seeded " . $count . " entries in " . $result . "\n";
                            }
                        }
                    }
                    break;
                case 'Entries':
                    if(is_array($option)) {
                        foreach ($option as $section => $count) {
                            $result = Seeder::$plugin->entries->generate($section, $count);
                            if($result) {
                                echo "Seeded " . $count . " entries in " . $result . "\n";
                            }
                        }
                    }
                break;
            }
        }
    }
}
