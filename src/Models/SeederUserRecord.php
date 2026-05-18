<?php

namespace Anubarak\Seeder\records;

use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * Class SeederUserRecord
 *
 * @package Anubarak\Seeder\records
 * @since   15.07.2024
 * @author  by Robin Schambach
 * @property string $userUid
 */
class SeederUserRecord extends BaseModel
{
    use HasUid;
    protected $table = 'seeder_user';
}
