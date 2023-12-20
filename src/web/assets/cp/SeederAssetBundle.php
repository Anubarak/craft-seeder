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

namespace anubarak\seeder\web\assets\cp;

use craft\web\AssetBundle;

/**
 * Class SeederAssetBundle
 *
 * @package anubarak\web\assets\cp
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class SeederAssetBundle extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@anubarak/seeder/web/assets/cp';
        $this->js = [
            'main.js',
        ];

        parent::init();
    }
}