<?php

namespace Anubarak\Seeder\Models;

use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * Class SeederUserRecord
 *
 * @package Anubarak\Seeder\Models
 * @since   15.07.2024
 * @author  by Robin Schambach
 * @property string $userUid
 */
class SeederUserRecord extends BaseModel
{
    use HasUid;
    protected $table = 'seeder_user';
}
