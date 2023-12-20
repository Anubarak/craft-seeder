<?php

use anubarak\seeder\models\EntryConfig;
use anubarak\seeder\models\FieldCallback;
use anubarak\seeder\models\Settings;
use craft\base\ElementInterface;
use craft\base\FieldInterface;

$config = (new Settings())
    ->fieldsConfig([
        new EntryConfig(
            'news',
            null,
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
    ]);

// required for Craft 4 (╯°□°）╯︵ ┻━┻)
return $config->toArray([], ['fieldsConfig'], false);