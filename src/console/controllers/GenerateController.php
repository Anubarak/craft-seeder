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

use anubarak\seeder\services\Assets;
use anubarak\seeder\services\Entries;
use anubarak\seeder\services\Users;
use craft\helpers\Console;
use craft\models\EntryType;
use craft\models\Section;
use Craft;
use craft\models\UserGroup;
use Illuminate\Support\Collection;
use yii\console\Controller;
use yii\console\ExitCode;

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
     * entry type handles
     *
     * @var string|null $type
     */
    public string|null $type = null;
    /**
     * @var string|int|null
     */
    public null|string|int $volume = null;
    /**
     * user group id or handle
     *
     * @var string|null $group
     */
    public null|string $group = null;
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
                return ['section', 'count', 'site', 'type'];
            case 'users':
                return ['group', 'count'];
            case 'assets':
                return ['volume', 'count'];
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
    public function actionEntries(Entries $entries): int
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

        $entryTypes = [];
        if ($this->type === null) {
            $types = Collection::make($section->getEntryTypes());
            $handles = $types
                ->map(fn(EntryType $type) => $type->handle)
                ->all();
            $selected = $this->multiSelect('Which entry type?', $handles);
            $entryTypes = $types
                ->filter(fn(EntryType $type) => in_array($type->handle, $selected, true))
                ->all();
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

        $this->ensureCount();

        Console::startProgress(0, $this->count);
        $entries->generate(
            $site,
            $section,
            $entryTypes,
            $this->count,
            function($done, $max) {
                Console::updateProgress($done, $max);
            }
        );
        Console::endProgress();

        return ExitCode::OK;
    }

    /**
     * Generates users for the specified usergroup
     *
     * @param \anubarak\seeder\services\Users $users
     *
     * @return int
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function actionUsers(Users $users): int
    {
        if (Craft::$app->getEdition() !== Craft::Pro) {
            echo "Users requires your Craft install to be upgrade to Pro. You can trial Craft Pro in the control panel\n";

            return ExitCode::CONFIG;
        }

        $groups = [];
        $userGroups = Collection::make(Craft::$app->getUserGroups()->getAllGroups());
        if ($this->group === null && $userGroups->count()) {
            $handles = $userGroups->map(fn(UserGroup $option) => $option->handle)->all();
            $selected = $this->multiSelect('Which user groups?', $handles, []);
            $groups = $userGroups
                ->filter(fn(UserGroup $type) => in_array($type->handle, $selected, true))
                ->all();
        } elseif ($this->group && $userGroups->count()) {
            $selected = explode(',', $this->group);
            $groups = $userGroups
                ->filter(fn(UserGroup $type) => in_array($type->handle, $selected, true))
                ->all();
        }

        $this->ensureCount();

        Console::startProgress(0, $this->count);
        $users->generate($groups, $this->count, function($done, $max) {
            Console::updateProgress($done, $max);
        });
        Console::endProgress();

        return ExitCode::OK;
    }

    /**
     * actionAssets
     *
     * @param \anubarak\seeder\services\Assets $assets
     *
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @author Robin Schambach
     * @since  25.06.2024
     */
    public function actionAssets(Assets $assets): int
    {
        if (!$this->volume) {
            $options = [];
            foreach (Craft::$app->getVolumes()->getAllVolumes() as $object) {
                $options[$object->handle] = $object->name;
            }

            $this->volume = $this->select('Which Volume?', $options);
        }

        if (ctype_digit($this->volume)) {
            $volume = Craft::$app->getVolumes()->getVolumeById((int) $this->volume);
        } else {
            $volume = Craft::$app->getVolumes()->getVolumeByHandle($this->volume);
        }

        $this->ensureCount(50);

        Console::startProgress(0, $this->count);
        $assets->generate($volume, $this->count, function($done, $max) {
            Console::updateProgress($done, $max);
        });
        Console::endProgress();

        return ExitCode::OK;
    }

    /**
     * ensureCount
     *
     * @author Robin Schambach
     * @since  25.06.2024
     */
    protected function ensureCount(int $max = null): void
    {
        if ($this->count === null) {
            $config = ['default' => 20];
            $message = 'How many would you like to create';
            if ($max) {
                $message .= ' (max:  ' . $max . ')';
                $config['validator'] = function($input, &$error) use ($max) {
                    if ((int) ($input) > $max) {
                        $error = 'Must be less than ' . $max;

                        return false;
                    }

                    return true;
                };
            }
            $this->count = $this->prompt($message, $config);
        } else {
            if ($max !== null && $this->count > $max) {
                $this->count = null;
                $this->ensureCount($max);
            }
        }
    }

    /**
     * multiSelect
     *
     * @param string       $message
     * @param array        $options
     * @param string|array $defaultValue
     *
     * @return array
     * @author Robin Schambach
     * @since  26.06.2024
     */
    protected function multiSelect(string $message, array $options, string|array $defaultValue = null): array
    {
        $this->stdout($message . PHP_EOL);

        foreach ($options as $option) {
            Console::stdout(' - ' . $option . PHP_EOL);
        }

        $selectedTypes = $this->prompt('select comma separated', [
            'default'   => $defaultValue === null ? implode(',', $options) : $defaultValue,
            'validator' => function($input, &$error) use ($options) {
                $types = explode(',', $input);
                if (empty($types)) {
                    return true;
                }

                $error = '';
                foreach ($types as $type) {
                    if (!in_array($type, $options, true)) {
                        $error = 'No option found with „' . $type . '“';
                    }
                }

                return !$error;
            }
        ]);

        return is_string($selectedTypes) ? explode(',', $selectedTypes) : $selectedTypes;
    }
}
