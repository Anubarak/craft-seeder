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

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\helpers\ElementHelper;
use verbb\hyper\base\ElementLink;

/**
 * Class Hyper
 *
 * @package anubarak\seeder\services\fields
 * @since   04/04/2024
 * @author  by Robin Schambach
 */
class Hyper extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\verbb\hyper\fields\HyperField|FieldInterface $field, ElementInterface $element = null)
    {
        $supported = [
            \verbb\hyper\links\Entry::class,
            \verbb\hyper\links\Asset::class,
            \verbb\hyper\links\Email::class,
            \verbb\hyper\links\Phone::class,
            \verbb\hyper\links\Url::class,
        ];

        $validTypes = [];
        $types = $field->getSettings()['linkTypes'] ?? [];
        foreach ($types as $type) {
            if ($type['enabled'] && in_array($type['type'], $supported, true)) {
                $validTypes[] = $type;
            }
        }

        $values = [];
        if ($field->multipleLinks) {
            $min = $field->minLinks ?? 1;
            $max = $field->maxLinks ?? 5;
            $max = random_int($min, $max);
            for ($i = 0; $i < $max; $i++) {
                $type = $this->factory->randomElement($validTypes);
                $values[] = $this->getLinkValue($type);
            }
        } else {
            // only single
            $type = $this->factory->randomElement($validTypes);
            $values[] = $this->getLinkValue($type);
        }

        return $values;
    }

    /**
     * getLinkValue
     *
     * @param array $config
     *
     * @return \verbb\hyper\base\Link
     * @throws \Random\RandomException
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  26.07.2024
     */
    protected function getLinkValue(array $config): \verbb\hyper\base\Link
    {
        /** @var \verbb\hyper\base\Link $class */
        $class = \Craft::createObject($config['type']);
        switch (true) {
            case $class instanceof ElementLink:
                /** @var \craft\elements\db\ElementQuery $query */
                $query = $class::elementType()::find();
                $source = $config['sources'][0] ?? '*';

                $source = ElementHelper::findSource($class::elementType(), $source);

                \Craft::configure($query, $source['criteria']);
                $element = $query->one();
                $class->linkValue = $element?->id;
                break;
            case $class instanceof \verbb\hyper\links\Email:
                $class->linkValue = $this->factory->email();
                break;
            case $class instanceof \verbb\hyper\links\Phone:
                $class->linkValue = $this->factory->phoneNumber();
                break;
            case $class instanceof \verbb\hyper\links\Url:
                $class->linkValue = $this->factory->url();
                break;
        }

        if (random_int(0, 10) <= 8) {
            $class->linkText = $this->factory->words(random_int(2, 8), true);
        }

        return $class;
    }
}