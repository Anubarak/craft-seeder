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


use Anubarak\Seeder\Seeder\Generators\Assets;
use Anubarak\Seeder\Seeder\Generators\Entries;
use Anubarak\Seeder\Seeder\Generators\Users;
use CraftCms\Cms\Asset\Data\Volume;
use CraftCms\Cms\Asset\Volumes;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Entry\Data\EntryType;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Entry\EntryTypes;
use CraftCms\Cms\Section\Data\Section;
use CraftCms\Cms\Section\Sections;
use CraftCms\Cms\Site\Data\Site;
use CraftCms\Cms\Site\Sites;
use CraftCms\Cms\User\Data\UserGroup;
use CraftCms\Cms\User\Elements\User;
use CraftCms\Cms\User\UserGroups;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

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
class Generate extends Command implements PromptsForMissingInput
{
    protected $signature   = 'element-seeder:generate 
    {type : generate entries}
    {count : The amount of elements to generate}
    {site : What site should the elements be generated for?}
    {volume? : What volume to use for assets}
    {section? : What section to use for entries}
    {entryTypes? : What entry types to use for entries}
    {userGroups? : What user groups to use for users}
    ';
    protected $description = 'Seed Elements';

    public function __construct(
        private readonly Sites      $sites,
        private readonly Sections   $sections,
        private readonly Volumes    $volumes,
        private readonly Elements   $elements,
        private readonly EntryTypes $entryTypes,
        private readonly UserGroups $userGroups,

        // generators
        private readonly Entries    $entries,
        private readonly Assets     $assets,
        private readonly Users      $users,
    ) {
        parent::__construct();
    }

    /**
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @author Robin Schambach
     * @since  18.05.26
     */
    public function handle(): int
    {
        switch ($this->argument('type')) {
            case 'assets':
                return $this->assets();
                break;
            case 'entries':
                return $this->entries();
                break;
            case 'users':
                return $this->users();
                break;
            default:
                throw new InvalidArgumentException('Invalid type provided: ' . $this->argument('type'));
        }
    }

    /**
     * Generates entries for the specified section
     */
    public function entries(): int
    {
        $count = $this->argument('count');
        $section = $this->sections->getSectionByHandle($this->argument('section'));

        if (!$section) {
            $this->error('No section found with „' . $this->argument('section') . '“' . PHP_EOL);

            return self::FAILURE;
        }

        $types = $this->argument('entryTypes');
        if (empty($types) || in_array('all', $types, true)) {
            $entryTypes = $section->getEntryTypes();
        } else {
            $entryTypes = (new Collection())
                ->map(fn(string $handle) => $this->entryTypes->getEntryTypeByHandle($handle))
                ->all();
        }

        $site = $this->sites->getSiteByHandle($this->argument('site'));


        $bar = $this->output->createProgressBar($count * count($entryTypes));
        $this->entries->generate(
            $site,
            $section,
            $entryTypes,
            $count,
            fn() => $bar->advance()
        );
        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$count} entries");

        return self::SUCCESS;
    }

    /**
     * Generates users for the specified groups
     * @return int
     * @throws \Throwable
     */
    public function users(): int
    {
        $groups = (new Collection($this->argument('userGroups')))
            ->map(fn(string $group) => $this->userGroups->getGroupByHandle($group))
            ->all();

        $count = $this->argument('count');
        $bar = $this->output->createProgressBar($count);
        $this->users->generate(
            $groups,
            $count,
            fn() => $bar->advance()
        );
        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$count} users");

        return self::SUCCESS;
    }

    /**
     * actionAssets
     *
     * @param \Anubarak\Seeder\services\Assets $assets
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @author Robin Schambach
     * @since  25.06.2024
     */
    public function assets(): int
    {
        $volume = $this->volumes->getVolumeByHandle($this->argument('volume'));
        $count = $this->argument('count');

        $bar = $this->output->createProgressBar($count);
        $this->assets->generate(
            $volume,
            $count,
            fn() => $bar->advance()
        );
        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$count} assets");

        return self::SUCCESS;
    }

    /**
     * @inheritdoc
     */
    protected function afterPromptingForMissingArguments(InputInterface $input, OutputInterface $output): void
    {
        // If they chose Asset, and didn't provide a volume parameter manually in CLI
        if ($input->getArgument('type') === 'assets' && !$input->getArgument('volume')) {
            $volume = select(
                label: 'What volume to use for assets?',
                options: $this->volumes->getAllVolumes()
                    ->mapWithKeys(fn(Volume $volume) => [$volume->handle => $volume->name])
                    ->all()
            );

            // Manually bind it back to the command input
            $input->setArgument('volume', $volume);
        }

        // If they chose Entry instead
        if ($input->getArgument('type') === 'entries' && !$input->getArgument('section')) {
            $section = select(
                label: 'What section to use for entries?',
                options: $this->sections->getAllSections()
                    ->mapWithKeys(fn(Section $section) => [$section->handle => $section->name])
                    ->all()
            );

            $input->setArgument('section', $section);

            if (!$input->getArgument('entryTypes')) {
                $sectionModel = $this->sections->getSectionByHandle($section);


                $availableTypes = (new Collection($sectionModel->getEntryTypes()))
                    ->mapWithKeys(fn(EntryType $entryType) => [$entryType->handle => $entryType->name])
                    ->all();

                if (count($availableTypes) > 1) {

                    $entryTypes = multiselect(
                        label: 'What entry types',
                        options: [
                            'all' => 'All',
                            ...$availableTypes
                        ]
                    );
                    $input->setArgument('entryTypes', $entryTypes);
                } else {
                    $input->setArgument('entryTypes', array_keys($availableTypes));
                }
            }
        }


        if ($input->getArgument('type') === 'users' && !$input->getArgument('userGroups')) {

            $allGroups = $this->userGroups->getAllGroups();
            if($allGroups->count()){
                $userGroups = multiselect(
                    label: 'Groups to use?',
                    options: $this->userGroups->getAllGroups()
                        ->mapWithKeys(fn(UserGroup $group) => [$group->handle => $group->name])
                        ->all()
                );
            } else {
                $userGroups = [];
            }

            // Manually bind it back to the command input
            $input->setArgument('userGroups', $userGroups);
        }
    }

    /**
     * @inheritdoc
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'type'  => fn() => select(
                label: 'Search for a user:',
                options: [
                    'entries' => Entry::displayName(),
                    'assets'  => Asset::displayName(),
                    'users'  => User::displayName(),
                ],
            ),
            'site'  => fn() => select(
                label: 'In what Site?:',
                options: $this->sites->getAllSites()
                    ->mapWithKeys(fn(Site $site) => [$site->handle => $site->getName()])
                    ->all()
            ),
            'count' => fn() => $this->ask('How many elements to generate?', 20),
        ];
    }
}
