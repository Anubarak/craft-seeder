<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\elements\Entry;
use craft\elements\User;
use craft\errors\ElementException;
use craft\helpers\Json;
use anubarak\seeder\Seeder;
use craft\models\Section;
use craft\models\Site;
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
     * generate
     *
     * @param \craft\models\Site        $site
     *
     * @param \craft\models\Section     $section
     * @param \craft\models\EntryType[] $entryTypes
     * @param int                       $count
     * @param callable|null             $cb
     *
     * @return bool
     * @author Robin Schambach
     * @since  19/12/2023
     */
    public function generate(
        Site     $site,
        Section  $section,
        array    $entryTypes = [],
        int      $count = 20,
        callable $cb = null
    ): bool {
        if (empty($entryTypes)) {
            $entryTypes = $section->getEntryTypes();
        }
        $current = 0;
        $total = count($entryTypes) * $count;
        $admin = User::find()->admin(true)->one();
        $db = Craft::$app->getDb();
        $elements = \Craft::$app->getElements();
        $seeder = Seeder::$plugin->getSeeder();

        foreach ($entryTypes as $entryType) {
            for ($x = 1; $x <= $count; $x++) {
                $current++;
                if($cb){
                    $cb($current, $total);
                }
                $transaction = $db->beginTransaction();

                try {
                    $entry = new Entry([
                        'sectionId' => (int) $section->id,
                        'typeId'    => $entryType->id,
                        'title'     => Seeder::$plugin->fields->Title(),
                        'siteId'    => $site->id,
                    ]);
                    $entry->setAuthorIds([$admin->id]);
                    $elements->saveElement($entry);
                    $seeder->saveSeededEntry($entry);
                    $entry->setScenario(Element::SCENARIO_LIVE);

                    if ($entryType->fieldLayoutId) {
                        $entry = $seeder->populateFields($entry);
                        if (!$elements->saveElement($entry)) {
                            Console::error(VarDumper::dumpAsString($entry->getErrors()));
                            throw new ElementException($entry, 'Could not save element due to validation errors');
                        }
                    }
                    $transaction->commit();
                } catch (\Throwable $throwable) {
                    $transaction->rollBack();
                    Craft::error($throwable);
                    Console::error('Error during seed');
                    Console::error($throwable->getMessage());

                    if ($throwable instanceof ElementException) {
                        Console::error(Json::encode($throwable->element->getErrors()));
                    }
                }
            }
        }

        return true;
    }
}