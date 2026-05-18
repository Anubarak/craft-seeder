<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Users seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Seeder\Generators;


use Anubarak\Seeder\Seeder\Seeder;
use Anubarak\Seeder\SeederServiceProvider;

use Craft;
use CraftCms\Cms\Element\Element;
use CraftCms\Cms\Element\Elements;
use CraftCms\Cms\User\Data\UserGroup;
use CraftCms\Cms\User\Elements\User;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Database\DatabaseManager;
use Illuminate\Log\LogManager;

/**
 * Class Users
 *
 * @package Anubarak\Seeder\Seeder
 * @since   26.06.2024
 * @author  by Robin Schambach
 */
#[Singleton]
class Users
{
    public function __construct(
        private readonly \CraftCms\Cms\User\Users $users,
        private readonly Seeder                   $seeder,
        private readonly Elements                 $elements,
        private readonly DatabaseManager          $db,
        private readonly LogManager               $logger,

    ) {
    }

    /**
     * @param UserGroup[] $userGroups
     *
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function generate(array $userGroups = [], int $count = 20, callable $cb = null): bool
    {
        $faker = $this->seeder->factory;

        $userGroupIds = [];
        foreach ($userGroups as $group) {
            $userGroupIds[] = $group->id;
        }

        $this->db->beginTransaction();
        try {
            for ($x = 1; $x <= $count; $x++) {
                $user = new User();
                $user->passwordResetRequired = true;
                $user->email = $faker->email();
                $user->username = $user->email;
                $user->firstName = $faker->firstName();
                $user->lastName = $faker->lastName();
                $this->elements->saveElement($user);
                $this->seeder->saveSeededUser($user);
                $user->setScenario(Element::SCENARIO_LIVE);
                $this->seeder->populateFields($user);
                $this->elements->saveElement($user);

                if (!empty($userGroupIds)) {
                    $this->users->assignUserToGroups($user->id, $userGroupIds);
                }
                if ($cb) {
                    $cb($x, $count);
                }
            }

            $this->db->commit();
        } catch (\Throwable $throwable) {
            $this->db->rollBack();
            throw $throwable;
        }

        return true;
    }
}