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

use anubarak\seeder\records\SeederAssetRecord;
use anubarak\seeder\records\SeederEntryRecord;
use anubarak\seeder\records\SeederUserRecord;
use anubarak\seeder\Seeder;

use Craft;
use anubarak\seeder\services\Seeder_EntriesService;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Seeder plugin
 *
 * This plugin allows you to quickly create dummy or test data that you can use while building your site.
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class CleanUpController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Clean up all seeded elements
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionAll()
    {
        $sections = Craft::$app->getSections();
        foreach($sections->getAllSections() as $section) {
            $seededEntries = SeederEntryRecord::findAll( [
                'section' => $section->id
            ] );
            if(count($seededEntries)) {
                Seeder::$plugin->weeder->entries($section->id);
            }
        }

	    Seeder::$plugin->weeder->assets();
	    Seeder::$plugin->weeder->users();

    }

}
