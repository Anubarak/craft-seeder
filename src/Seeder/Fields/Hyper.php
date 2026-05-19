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

namespace Anubarak\Seeder\Seeder\Fields;

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Element\ElementSources;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Support\Typecast;
use verbb\hyper\base\ElementLink;

/**
 * Class Hyper
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   04/04/2024
 * @author  by Robin Schambach
 */
class Hyper extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\verbb\hyper\fields\HyperField|FieldInterface $field, ElementInterface|null $element = null)
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
     * @author Robin Schambach
     * @since  26.07.2024
     */
    protected function getLinkValue(array $config): \verbb\hyper\base\Link
    {
        /** @var \verbb\hyper\base\Link $class */
        $class = \Craft::createObject($config['type']);
        switch (true) {
            case $class instanceof ElementLink:
                $query = $class::elementType()::find();
                $source = $config['sources'][0] ?? '*';

                $source = app(ElementSources::class)->findSource($class::elementType(), $source);

                Typecast::configure($query, $source['criteria']);
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