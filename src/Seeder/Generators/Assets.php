<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2024 Robin Schambach|Secondred Newmedia GmbH
 */

namespace Anubarak\Seeder\Seeder\Generators;

use Anubarak\Seeder\Seeder\Seeder;
use CraftCms\Cms\Asset\Data\Volume;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Asset\Folders;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\Element\Exceptions\ElementException;
use CraftCms\Cms\Support\File;
use CraftCms\Cms\Support\Json;
use CraftCms\Cms\Support\Path;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Facades\Http;

/**
 * Class Assets
 *
 * @package Anubarak\Seeder\Seeder
 * @since   25.06.2024
 * @author  by Robin Schambach
 */
#[Singleton]
readonly class Assets
{
    public function __construct(
        private Folders  $folders,
        private Elements $elements,
        private Seeder   $seeder,
        private Path     $path,

    ) {
    }

    /**
     * generate
     *
     * @param \CraftCms\Cms\Asset\Data\Volume $volume
     * @param int                             $count
     * @param callable|null                   $cb
     *
     * @return void
     * @throws \CraftCms\Cms\Element\Exceptions\ElementException
     * @throws \ErrorException
     * @throws \Random\RandomException
     * @throws \Throwable
     * @author Robin Schambach
     * @since  25.06.2024
     */
    public function generate(Volume $volume, int $count, callable $cb = null): void
    {
        $folder = $this->folders->getRootFolderByVolumeId($volume->id);

        $res = Http::get('https://picsum.photos/v2/list', [
                'limit' => $count,
                'page'  => random_int(2, 6)
        ]);

        $imageData = Json::decode($res->getBody()->getContents());
        $tmpPath = $this->path->temp() . DIRECTORY_SEPARATOR;
        foreach ($imageData as $i => $data) {
            $fileName = $this->seeder->factory->words(3, true) . '.jpg';
            $fileNameNormalized = File::sanitizeFilename($fileName);
            $content = file_get_contents($data['download_url']);
            $tmpFilePath = $tmpPath . uniqid($fileNameNormalized);
            File::writeToFile($tmpFilePath, $content);
            $asset = new Asset();
            $asset->tempFilePath = $tmpFilePath;
            $asset->setFilename($fileName);
            $asset->newFolderId = $folder->id;
            $asset->setVolumeId($folder->volumeId);
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);

            if (!$this->elements->saveElement($asset)) {
                throw new ElementException(
                    $asset,
                    'could not save asset due to errors: ' . json_encode($asset->errors()->all())
                );
            }

            $this->seeder->saveSeededAsset($asset);


            if ($cb) {
                $cb($i + 1, $count);
            }
        }
    }
}