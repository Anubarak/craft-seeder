<?php

namespace anubarak\seeder\elements\actions;

use anubarak\seeder\Seeder;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;
use craft\elements\db\OrderByPlaceholderExpression;

/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2024 Robin Schambach|Secondred Newmedia GmbH
 *
 * @property-read null|string $triggerHtml
 * @property-read string      $triggerLabel
 */
class NumerizeAction extends ElementAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return \Craft::t('element-seeder', 'Numerize Element(s)');
    }

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        // hacky, I don't like this, but Craft will skip orderBy in case it's not the Placeholder Expression
        // and you cannot set it back...
        // Entry::find()->orderBy(null)
        // will prevent our fixed order by expression ¯\_(ツ)_/¯
        if(empty($query->orderBy)){
            $query->orderBy([new OrderByPlaceholderExpression()]);
        }
        $query->fixedOrder();
        Seeder::$plugin->getSeeder()->numerateTitle($query);

        $this->setMessage('changed titles');

        return true;
    }
}