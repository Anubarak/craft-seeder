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

namespace anubarak\seeder\web\assets\style;

use craft\web\AssetBundle;

class SeederStyleBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@anubarak/seeder/web/assets/style';
        $this->css = [
            'style.css',
        ];
        parent::init();
    }
}