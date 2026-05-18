<?php

use Anubarak\Seeder\Data\EntryConfig;
use Anubarak\Seeder\Data\FieldCallback;
use Anubarak\Seeder\Settings;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use craft\base\FieldInterface;

return [
    'fieldsConfig' => [
        new EntryConfig(
            'news',
            [
                (new FieldCallback('date'))
                    ->setCallable(
                        static function(
                            \Faker\Generator $faker,
                            FieldInterface   $field,
                            ElementInterface $element
                        ) {
                            return new DateTime();
                        }
                    ),
                (new FieldCallback('date2'))
                    ->setCallable(
                        static function(
                            \Faker\Generator $faker,
                            FieldInterface   $field,
                            ElementInterface $element
                        ) {
                            $date = (clone $element->getFieldValue('date'));
                            $date->modify('+1 day');

                            return $date;
                        }
                    ),
                (new FieldCallback('headline'))
                    ->setFakerMethod('text'),
            ]
        )
    ]
];