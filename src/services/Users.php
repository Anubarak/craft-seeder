<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Users seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\services;

use craft\base\Element;
use craft\elements\User;
use anubarak\seeder\Seeder;

use Craft;
use craft\base\Component;

/**
 * Class Users
 *
 * @package anubarak\seeder\services
 * @since   26.06.2024
 * @author  by Robin Schambach
 */
class Users extends Component
{
    /**
     * @param \craft\models\UserGroup[] $userGroups
     *
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function generate(array $userGroups = [], int $count = 20, callable $cb = null): bool
    {
        $seeder = Seeder::$plugin->getSeeder();
        $faker = $seeder->factory;

        $elements = Craft::$app->getElements();
        $users = Craft::$app->getUsers();

        $userGroupIds = [];
        foreach ($userGroups as $group) {
            $userGroupIds[] = $group->id;
        }

        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            for ($x = 1; $x <= $count; $x++) {
                $user = new User();
                $user->passwordResetRequired = true;
                $user->email = $faker->email();
                $user->username = $user->email;
                $user->firstName = $faker->firstName();
                $user->lastName = $faker->lastName();
                $elements->saveElement($user);
                $seeder->saveSeededUser($user);
                $user->setScenario(Element::SCENARIO_LIVE);
                $seeder->populateFields($user);
                $elements->saveElement($user);

                if (!empty($userGroupIds)) {
                    $users->assignUserToGroups($user->id, $userGroupIds);
                }
                if ($cb) {
                    $cb($x, $count);
                }
            }

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }

        return true;
    }
}