<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2023 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\helpers\Db;
use yii\db\Expression;

/**
 * Class Assets
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Assets extends BaseField
{

    public function __construct(\Faker\Generator $factory, protected \anubarak\seeder\services\Assets $assetService)
    {
        parent::__construct($factory);
    }

    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Assets|FieldInterface $field, ElementInterface $element = null)
    {
        $source = $field->sources;
        $volumeIds = [];
        if ($source !== '*') {

            if (!is_array($source)) {
                $source = [$source];
            }

            foreach ($source as $s) {
                $volumeUid = str_replace('folder:', '', $s);
                $volumeId =  Db::idByUid(\craft\db\Table::VOLUMES, $volumeUid);

                // in case there are no images in that volume yet -> generate a few...
                $tmpImages = Asset::find()->volumeId($volumeId)->exists();
                if(!$tmpImages){
                    $volume = \Craft::$app->getVolumes()->getVolumeById($volumeId);
                    $this->assetService->generate($volume, 50);
                }


                $volumeIds[] =$volumeId;
            }
        } else if(!Asset::find()->exists()) {
            // no image -> seed one for each volume
            foreach (\Craft::$app->getVolumes()->getAllVolumes() as $volume){
                $this->assetService->generate($volume, 50);
            }
        }

        $limit = 2;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }


        $query = Asset::find()
            ->limit(random_int(1, $limit))
            ->orderBy(\anubarak\seeder\helpers\DB::random());
        if ($volumeIds) {
            $query->volumeId($volumeIds);
        }

        if ($field->allowedKinds) {
            $query->kind($field->allowedKinds);
        }

        return $query->ids();
    }

    protected function seedImages()
    {

    }
}