<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\seeder\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\elements\Entry;
use craft\elements\User;
use craft\helpers\Json;
use studioespresso\seeder\Seeder;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * SeederService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Entries extends Component
{
    /**
     * @param null $section
     * @param int  $count
     *
     * @return bool|string|null
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function generate($section = null, $count = 20, $site = null)
    {
        if (ctype_digit($section)) {
            $section = Craft::$app->sections->getSectionById((int)$section);
        } else {
            $section = Craft::$app->sections->getSectionByHandle($section);
        }

        if (!$section) {
            echo "Section not found\n";
            return false;
        }

        if($site && is_string($site)){
            $site = Craft::$app->getSites()->getSiteByHandle($site);
        }

        if($site === null){
            $site = Craft::$app->getSites()->getPrimarySite();
        }

        $entryTypes = $section->getEntryTypes();
        $current = 0;
        $total = count($entryTypes) * $count;
        $admin = User::find()->admin(true)->one();
        Console::startProgress($current, $count);
        foreach ($section->getEntryTypes() as $entryType) {
            for ($x = 1; $x <= $count; $x++) {
                $current++;
                Console::updateProgress($current, $count);
                if($entryType->fieldLayoutId) {
                    $typeFields = Craft::$app->fields->getFieldsByLayoutId($entryType->getFieldLayoutId());
                }
                $entry = new Entry([
                    'sectionId' => (int)$section->id,
                    'typeId' => $entryType->id,
                    'title' => Seeder::$plugin->fields->Title(),
                    'siteId' => $site->id,
                ]);
                $entry->authorId = $admin->id;
                Craft::$app->getElements()->saveElement($entry);
                Seeder::$plugin->seeder->saveSeededEntry($entry);
                $entry->setScenario(Element::SCENARIO_LIVE);
                if($entryType->fieldLayoutId) {
                    $entry = Seeder::$plugin->seeder->populateFields($typeFields, $entry);
                    if(!Craft::$app->getElements()->saveElement($entry)){
                        Console::error(VarDumper::dumpAsString($entry->getErrors()));
                        Craft::$app->getElements()->deleteElement($entry);
                    }
                }
            }
        }
        Console::endProgress();
        return $section->name;

    }

}