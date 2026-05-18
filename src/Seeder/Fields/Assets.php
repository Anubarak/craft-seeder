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

namespace Anubarak\Seeder\Seeder\Fields;

use CraftCms\Cms\Asset\Elements\Asset;
use Craftcms\Cms\Field\Assets as AssetsField;
use CraftCms\Cms\Asset\Volumes;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;

/**
 * Class Assets
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Assets extends BaseField
{

    public function __construct(
        \Faker\Generator $factory,
        Fields $fields,
        private readonly \Anubarak\Seeder\Seeder\Generators\Assets $assetService,
        private readonly Volumes $volumes,
    )
    {
        parent::__construct($factory, $fields);
    }

    /**
     * @inheritDoc
     */
    public function generate(AssetsField|FieldInterface $field, ElementInterface|null $element = null)
    {
        $source = $field->sources;
        $volumeIds = [];
        if ($source !== '*') {

            if (!is_array($source)) {
                $source = [$source];
            }

            foreach ($source as $s) {
                $volumeUid = str_replace('folder:', '', $s);
                $volume = $this->volumes->getVolumeByUid($volumeUid);

                // in case there are no images in that volume yet -> generate a few...
                $tmpImages = Asset::find()->volume($volume)->exists();
                if(!$tmpImages){
                    $this->assetService->generate($volume, 50);
                }


                $volumeIds[] = $volume->id;
            }
        } else if(!Asset::find()->exists()) {
            // no image -> seed one for each volume
            foreach ($this->volumes->getAllVolumes()->all() as $volume){
                $this->assetService->generate($volume, 50);
            }
        }

        $limit = 2;
        if ($field->maxRelations) {
            $limit = $field->maxRelations;
        }


        $query = Asset::find()
            ->limit(random_int(1, $limit))
            ->inRandomOrder();
        if ($volumeIds) {
            $query->volumeId($volumeIds);
        }

        if ($field->allowedKinds) {
            $query->kind($field->allowedKinds);
        }

        return $query->ids();
    }
}