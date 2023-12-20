# Seeder plugin for Craft CMS

## Usage

Seeder allows you to quickly create dummy entries through the command line. And you can just as easily remove the dummy data when you're done building the site.
With the plugin installed, running `php craft element-seeder/generate/entries` create entries

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require anubarak/craft-seeder

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for "Seeder".

### Entries (Section ID/handle, count)

Use the command below, followed by the ``--section`` option and the ``--number`` of entries you want to create (defaults to 20 if ommited). This command works with both section ID and handle. 

```Shell
php craft element-seeder/generate/entries --section=news --count=15
```

## Clean up
Once you're done building out the site, the plugin gives you an easy way to remove the dummy data (entries, assets, categories and users). This can be done through the CP (click the Seeder section the sidebar) or through the command line with the following command:

```Shell
php craft element-seeder/clean-up
```

## Configuration options

you can include custom configurations for each layout

```php
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

// required for Craft 4 since they'll do an array_merge
return $config->toArray([], ['fieldsConfig'], false);
```
Would create a custom callback while seeding fields for entries in the section `news` for fields 
`date`, `date2` and `text`. 
Text would call the `Faker` function `$faker->text` and `date` and `date2` would call the callback function in order to 
populate fields with custom conditions