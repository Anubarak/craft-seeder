<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Users seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace studioespresso\seeder\services;

use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\User;
use craft\errors\FieldNotFoundException;
use craft\helpers\Console;
use Faker\Factory;
use Faker\Provider\Person;
use studioespresso\seeder\Seeder;

use Craft;
use craft\base\Component;
use yii\base\Model;

/**
 * Users Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Users extends Component
{
    /**
     * @param null $group
     * @param int  $count
     *
     * @return bool
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \Throwable
     */
    public function generate($group = null, $count = 20): bool
    {
        $userGroup = null;
        if($group !== null){
            if (ctype_digit($group)) {
                $userGroup = Craft::$app->userGroups->getGroupById((int)$group);
            } else {
                $userGroup = Craft::$app->userGroups->getGroupByHandle($group);
            }
        }

        $faker = Factory::create();

        $fields = Craft::$app->fields->getFieldsByElementType('craft\elements\User');
        $current = 0;
        Console::startProgress($current, $count);
        for ($x = 1; $x <= $count; $x++) {
            $user = new User();
            $user->passwordResetRequired = true;
            $user->email = $faker->email;
            $user->username = $user->email;
            $user->firstName = $faker->firstName;
            $user->lastName = $faker->lastName;
            Craft::$app->elements->saveElement($user);
            Seeder::$plugin->seeder->saveSeededUser($user);
            Seeder::$plugin->seeder->populateFields($fields, $user);
            Craft::$app->elements->saveElement($user);
            if($userGroup !== null){
                Craft::$app->users->assignUserToGroups($user->id, [$userGroup->id]);
            }
            $current++;
            Console::updateProgress($current, $count);
        }
        Console::endProgress();

        return true;
    }

}