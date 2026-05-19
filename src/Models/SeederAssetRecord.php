<?php

namespace Anubarak\Seeder\Models;

use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * Class SeederAssetRecord
 *
 * @package Anubarak\Seeder\Models
 * @since   26.06.2024
 * @author  by Robin Schambach
 * @property string $assetUid
 */
class SeederAssetRecord extends BaseModel
{
    use HasUid;

    protected $table = 'seeder_assets';
}
