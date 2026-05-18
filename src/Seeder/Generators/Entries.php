<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Seeder\Generators;


use Anubarak\Seeder\Seeder\Fields\Fields;
use Anubarak\Seeder\Seeder\Seeder;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Element\Exceptions\ElementException;
use CraftCms\Cms\Element\Validation\ElementRules;
use CraftCms\Cms\Entry\Data\EntryType;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Section\Data\Section;
use CraftCms\Cms\Site\Data\Site;
use CraftCms\Cms\User\Elements\User;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;

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
#[Singleton]
class Entries
{
    public function __construct(
        private readonly Elements        $elements,
        private readonly Seeder          $seeder,
        private readonly DatabaseManager $db,
        private readonly LogManager      $logger,
        private readonly Fields          $fields,

    ) {
    }

    /**
     * generate
     *
     * @param Site          $site
     *
     * @param Section       $section
     * @param EntryType[]   $entryTypes
     * @param int           $count
     * @param callable|null $cb
     *
     * @return bool
     * @throws \CraftCms\Cms\Element\Exceptions\ElementException
     * @throws \Throwable
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
        $admin = User::find()
            ->admin(true)
            ->one();

        foreach ($entryTypes as $entryType) {
            for ($x = 1; $x <= $count; $x++) {
                $current++;
                if ($cb) {
                    $cb($current, $total);
                }
                $this->db->beginTransaction();

                try {
                    $entry = new Entry([
                        'sectionId' => (int) $section->id,
                        'typeId'    => $entryType->id,
                        'title'     => $this->fields->Title(),
                        'siteId'    => $site->id,
                    ]);
                    $entry->setAuthorIds([$admin->id]);
                    $this->elements->saveElement($entry);
                    $this->seeder->saveSeededEntry($entry);
                    $entry->ruleset->useScenario(ElementRules::SCENARIO_LIVE);

                    if ($entryType->getFieldLayout()) {
                        $entry = $this->seeder->populateFields($entry);
                        if (!$this->elements->saveElement($entry)) {
                            throw new ElementException($entry, 'Could not save element due to validation errors');
                        }
                    }
                    $this->db->commit();
                } catch (\Throwable $throwable) {
                    $this->db->rollBack();
                    $this->logger->error($throwable);

                    throw $throwable;
                }
            }
        }

        return true;
    }
}