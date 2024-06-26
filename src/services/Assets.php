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

namespace anubarak\seeder\services;

use anubarak\seeder\Seeder;
use craft\elements\Asset;
use craft\errors\ElementException;
use craft\helpers\FileHelper;
use craft\models\Volume;
use yii\base\Component;
use yii\helpers\Json;
use yii\helpers\VarDumper;

/**
 * Class Assets
 *
 * @package anubarak\seeder\services
 * @since   25.06.2024
 * @author  by Robin Schambach
 */
class Assets extends Component
{
    /**
     * generate
     *
     * @param \craft\models\Volume $volume
     * @param int                  $count
     * @param callable|null        $cb
     *
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     * @author Robin Schambach
     * @since  25.06.2024
     */
    public function generate(Volume $volume, int $count, callable $cb = null): void
    {
        $folder = \Craft::$app->getAssets()->getRootFolderByVolumeId($volume->id);
        $elements = \Craft::$app->getElements();

        $res = \Craft::createGuzzleClient()->get('https://picsum.photos/v2/list', [
            'query' => [
                'limit' => $count
            ]
        ]);

        $imageData = Json::decode($res->getBody()->getContents());
        $tmpPath = \Craft::$app->getPath()->getTempPath() . DIRECTORY_SEPARATOR;
        $seeder = Seeder::$plugin->getSeeder();

        foreach ($imageData as $i => $data){
            $fileName = $seeder->factory->words(3, true) . '.jpg';
            $fileNameNormalized = FileHelper::sanitizeFilename($fileName);
            $content = file_get_contents($data['download_url']);
            $tmpFilePath = $tmpPath . uniqid($fileNameNormalized);
            FileHelper::writeToFile($tmpFilePath, $content);
            $asset = new Asset();
            $asset->tempFilePath = $tmpFilePath;
            $asset->setFilename($fileName);
            $asset->newFolderId = $folder->id;
            $asset->setVolumeId($folder->volumeId);
            $asset->avoidFilenameConflicts = true;
            $asset->setScenario(Asset::SCENARIO_CREATE);

            if(!$elements->saveElement($asset)){
                throw new ElementException($asset, 'could not save asset due to errors: ' . VarDumper::dumpAsString($asset->getErrors()));
            }

            $seeder->saveSeededAsset($asset);


            if($cb){
                $cb($i + 1, $count);
            }
        }
    }
}