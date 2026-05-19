# Seeder plugin for Craft CMS

## Usage

Seeder allows you to quickly create dummy entries through the command line. And you can just as easily remove the dummy data when you're done building the site.
With the plugin installed, running `php artisan element-seeder:generate`

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require anubarak/craft-seeder

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for "Seeder".

### Generate Entries/Assets/Users

```Shell
php artisan element-seeder:generate
```

will guide you through the wizard.

## Clean up
Once you're done building out the site, the plugin gives you an easy way to remove the dummy data (entries, assets, categories and users). This can be done through the CP (click the Seeder section the sidebar) or through the command line with the following command:

```Shell
php craft element-seeder/clean-up
```

## Configuration options

you can include custom configurations for each layout

```php
use Anubarak\Seeder\Data\EntryConfig;
use Anubarak\Seeder\Data\FieldCallback;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;

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
```
Would create a custom callback while seeding fields for entries in the section `news` for fields 
`date`, `date2` and `text`. 
Text would call the `Faker` function `$faker->text` and `date` and `date2` would call the callback function in order to 
populate fields with custom conditions

## Populate Elements via CP

### Matrix with unique Value

When an entry has a matrix field you can populate these matrix fields with blocks or with unique values in case their field layout allows it.
For example when a block has a Dropdown field with 3 options and a Lightswitch field (with 2 options) you can generate 6 unique value combinations.
In that case it will automatically create 6 blocks with each combination. Otherwise it will create the number of blocks set in the number field

![seed-matrix-1.png](resources/seed-matrix-1.png)

![seed-matrix-2.png](resources/seed-matrix-2.png)

### Populate fields

Another option is to seed content in general.

![seed-content-1.png](resources/seed-content-1.png)

You can choose which fields should be populated individually and Craft will seed it

![seed-content-2.png](resources/seed-content-2.png)

### Populate multiple elements

The same can be done with multiple elements via element index

![seed-element-index.png](resources/seed-element-index.png)

## Events

### Register Field Type Event

to include custom fields, you can use the `\Anubarak\Seeder\Events\RegisterFieldTypeEvent` event.

```php
\Illuminate\Support\Facades\Event::listen(
    \Anubarak\Seeder\Events\RegisterFieldTypeEvent::class,
    function(\Anubarak\Seeder\Events\RegisterFieldTypeEvent $event){
    $event->types['my\field\Class'] = MyCustomField::class;
});

```
My Custom field could then look like the following
```php
use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;

class PlainText extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\PlainText|FieldInterface $field, ElementInterface|null $element = null)
    {
        if(!$field->multiline){
            return $this->factory->text($field->charLimit ?: 200);
        }

        return $this->factory->realText($field->charLimit ?: 200);
    }
}
```

### Register Unique Field Event

To register a unique field that should be able to the unique matrix fields

```php
\Illuminate\Support\Facades\Event::listen(
    \Anubarak\Seeder\Events\RegisterUniqueFieldEvent::class,
    static function(\Anubarak\Seeder\Events\RegisterUniqueFieldEvent $event){
        $event->fields[] = MyCustomUniqueField::class;
    }
);
```
```php
class DropdownUniqueField implements UniqueFieldInterface
{
    /**
     * @param \CraftCms\Cms\Field\BaseOptionsField $field
     *
     * @inheritDoc
     */
    public function getDescription(Field $field): string
    {
        $options = [];
        foreach ($field->options as $option) {
            $options[] = '<code>' . $option['value'] . '</code>';
        }

        return 'Options: ' . join(' | ', $options);
    }

    /**
     * @param BaseOptionsField $field
     *
     * @inheritDoc
     */
    public function getValues(Field $field): array
    {
        $options = [];
        foreach ($field->options as $option) {
            $options[] = $option['value'];
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function getFieldClass(): string
    {
        return BaseOptionsField::class;
    }
}
```
You can take a look at the existing unique Fields