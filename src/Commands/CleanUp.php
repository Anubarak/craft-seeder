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

use Anubarak\Seeder\Seeder\Weeder;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Section\Sections;
use CraftCms\Cms\User\Elements\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

/**
 * Seeder plugin
 *
 * This plugin allows you to quickly create dummy or test data that you can use while building your site.
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class CleanUp extends Command implements PromptsForMissingInput
{
    protected $signature   = 'element-seeder:clean-up {types : what should be cleaned}';
    protected $description = 'Delete all seeded elements';

    public function __construct(
        private readonly Sections $sections,
        private readonly Weeder   $weeder,
    ) {
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
        $types = new Collection($this->argument('types'));


        $key = $types->search('all');
        $all = $key !== false ? (bool)$types->pull($key) : false;

        $key = $types->search('users');
        $users = $key !== false ?  (bool)$types->pull($key) : false;

        $key = $types->search('assets');
        $assets = $key !== false ? (bool)$types->pull($key) : false;

        foreach ($this->sections->getAllSections()->all() as $section) {

            if ($all || $types->search($section->handle) !== false) {
                $this->info("Clear {$section->name}");
                $this->newLine();
                $this->weeder->entries($section->id);
            }
        }
        if ($assets || $all) {
            $this->info("Clear Assets");
            $this->newLine();
            $this->weeder->assets();
        }
        if ($users || $all) {
            $this->info("Clear Users");
            $this->newLine();
            $this->weeder->users();
        }

        return self::SUCCESS;
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'types' => fn() => multiselect(
                label: 'What should be cleaned:',
                options: [
                    'all'    => 'All',
                    ... $this->sections->getAllSections()
                        ->mapWithKeys(fn($section) => [$section->handle => $section->name])
                        ->all(),
                    'assets' => Asset::displayName(),
                    'users'  => User::displayName(),
                ],
            ),
        ];
    }
}
