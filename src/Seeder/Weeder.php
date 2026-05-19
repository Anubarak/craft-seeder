<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Weeder service for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Seeder;

use Anubarak\Seeder\Models\SeederAssetRecord;
use Anubarak\Seeder\Models\SeederEntryRecord;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Entry\Elements\Entry;
use Anubarak\Seeder\Models\SeederUserRecord;
use CraftCms\Cms\Section\Sections;
use CraftCms\Cms\User\Elements\User;
use Illuminate\Container\Attributes\Singleton;

/**
 * Weeder Service
 *
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
#[Singleton]
class Weeder
{
    public function __construct(
        private readonly Elements $elements,
        private Sections $sections,
    )
    {
    }

    /**
     * entries
     *
     * @param $sectionId
     *
     * @return void
     * @throws \Throwable
     * @author Robin Schambach
     * @since  04/04/2024
     */
    public function entries(int $sectionId): void
    {
        $seededEntries = SeederEntryRecord::query()
            ->where('section', '=' , $sectionId)
            ->get();
        $section = $this->sections->getSectionById($sectionId);
        foreach ($seededEntries as $seededEntry) {
            $entry = Entry::find()
                ->uid($seededEntry->entryUid)
                ->section($section->handle)
                ->one();
            if ($entry) {
                $this->elements->deleteElement($entry);
            }
            $seededEntry->delete();
        }
    }

    public function assets(): void
    {
        $seededAssets = SeederAssetRecord::all();
        foreach ($seededAssets->all() as $seededAsset) {
            $asset = Asset::find()
                ->uid($seededAsset->assetUid)
                ->one();
            if ($asset) {
                $this->elements->deleteElement($asset);
            }
            $seededAsset->delete();
        }
    }

    public function users(): void
    {
        $seededUsers = SeederUserRecord::all();
        foreach ($seededUsers->all() as $seededUser) {
            $user = User::find()
                ->uid($seededUser->userUid)
                ->one();
            if ($user) {
                $this->elements->deleteElement($user);
            }
            $seededUser->delete();
        }
    }
}