<?php

namespace Anubarak\Seeder\Models;

use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * Class SeederEntryRecord
 *
 * @package Anubarak\Seeder\Models
 * @since   19/12/2023
 * @author  by Robin Schambach
 *
 * @property string entryUid
 * @property int    section
 */
class SeederEntryRecord extends BaseModel
{
    use HasUid;

    protected $table = 'seeder_entries';
}
