<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Commands;

use Anubarak\Seeder\records\SeederEntryRecord;
use Anubarak\Seeder\SeederServiceProvider;
use Anubarak\Seeder\services\Weeder;
use CraftCms\Cms\Section\Sections;
use Illuminate\Console\Command;

/**
 * Seeder plugin
 *
 * This plugin allows you to quickly create dummy or test data that you can use while building your site.
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class CleanUp extends Command
{

    public function __construct(
        private readonly Sections $sections,
        private readonly Weeder $weeder,

    )
    {
        parent::__construct();
    }

    /**
     * Clean up all seeded elements
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function handle(): int
    {
        foreach ($this->sections->getAllSections()->all() as $section) {
            $seededEntries = SeederEntryRecord::findAll([
                'section' => $section->id
            ]);
            if (count($seededEntries)) {
                SeederServiceProvider::$plugin->weeder->entries($section->id);
            }
        }

        SeederServiceProvider::$plugin->weeder->assets();
        SeederServiceProvider::$plugin->weeder->users();

        return ExitCode::OK;
    }
}
