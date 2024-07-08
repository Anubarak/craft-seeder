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

namespace anubarak\seeder\console\controllers;

use anubarak\seeder\Seeder;
use craft\console\Controller;
use craft\helpers\ArrayHelper;
use craft\helpers\Db;
use yii\console\ExitCode;

/**
 * Class PopulateController
 *
 * @package anubarak\seeder\console\controllers
 * @since   08.07.2024
 * @author  by Robin Schambach
 */
class PopulateController extends Controller
{
    /**
     * comma separated fields that should be populated
     *
     * @var string|null $fields
     */
    public ?string $fields = null;

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);
        if ($actionID === 'index') {
            $options[] = 'fields';
        }

        return $options;
    }

    /**
     * populate an element by it's ID, optionally pass a comma separated list of fields
     *
     * @param int $id
     *
     * @return int|void
     * @throws \yii\base\ExitException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\NotSupportedException
     * @author Robin Schambach
     * @since  08.07.2024
     */
    public function actionIndex(int $id)
    {
        $element = \Craft::$app->getElements()->getElementById($id);
        if (!$element) {
            $this->stderr('No element found with ID: ' . $id . PHP_EOL);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $fields = [];
        if ($this->fields) {
            $fields = ArrayHelper::toArray($this->fields);
        }

        $seeder = Seeder::$plugin->getSeeder();
        $seeder->populateFields($element, $fields);
        if (!\Craft::$app->getElements()->saveElement($element)) {
            $this->stderr('Failed to save element with ID: ' . $id . PHP_EOL);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }
}