<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\console\controllers;

use craft\elements\User;
use craft\errors\FieldNotFoundException;
use craft\helpers\Json;
use craft\models\Section;
use secondred\base\fields\IncrementField;
use anubarak\seeder\Seeder;

use Craft;
use anubarak\seeder\services\Seeder_EntriesService;
use anubarak\seeder\services\SeederService;
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
     * @var null|string|int $section
     */
    public null|string|int $section = null;
    /**
     * user group id or handle
     *
     * @var string|int|null $group
     */
    public null|string|int $group = null;
    /**
     * Number of entries to be seeded
     * @var int|null $count
     */
    public int|null $count = null;
    /**
     * site handle or id
     *
     * @var string|null|int $site
     */
    public string|null|int $site = null;
    // Public Methods
    // =========================================================================


    /**
     * @inheritdoc
     */
    public function options($actionId)
    {
        switch ($actionId) {
            case 'entries':
                return ['section', 'count', 'site'];
            case 'users':
                return ['group', 'count'];
        }
    }

    /**
     * Generates entries for the specified section
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return int
     * @throws \craft\errors\SiteNotFoundException
     */
    public function actionEntries(): int
    {
        if (!$this->section) {
            $options = [];
            foreach (Craft::$app->getEntries()->getAllSections() as $section) {
                if ($section->type !== Section::TYPE_SINGLE) {
                    $options[$section->handle] = $section->name;
                }
            }

            $this->section = $this->select('Which section?', $options);
        }

        if (ctype_digit($this->section)) {
            $section = Craft::$app->getEntries()->getSectionById((int) $this->section);
        } else {
            $section = Craft::$app->getEntries()->getSectionByHandle($this->section);
        }


        if (!$section) {
            $this->stderr('No section found with „' . $this->section . '“' . PHP_EOL);

            return ExitCode::OK;
        }

        $site = null;
        if ($this->site) {
            if (ctype_digit($this->site)) {
                $site = Craft::$app->getSites()->getSiteById((int) $this->site);
            } else {
                $site = Craft::$app->getSites()->getSiteByHandle($this->site);
            }
        }

        // fallback nothing set -> use default
        if ($site === null) {
            $site = Craft::$app->getSites()->getPrimarySite();
        }

        if($this->count === null){
            $this->count = $this->prompt('How many would you like to create', ['default' => 20]);
        }

        Seeder::$plugin->entries->generate(
            $site,
            $section,
            $this->count
        );

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
}
