<?php

namespace Anubarak\Seeder\Seeder\Fields;

use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use CraftCms\Cms\Entry\Elements\Entry;

class CraftLink extends BaseField
{
    /**
     * @param \CraftCms\Cms\Field\Link                                    $field
     * @param \CraftCms\Cms\Element\Contracts\ElementInterface|null $element
     *
     * @return array
     * @throws \Random\RandomException
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface|null $element = null)
    {

        $type = $this->factory->randomElement($field->getLinkTypes());
        $value = match ($type::class) {
            \CraftCms\Cms\Field\LinkTypes\Entry::class => Entry::find()
                ->limit(1)
                ->inRandomOrder()
                ->one()->id,
            \CraftCms\Cms\Field\LinkTypes\Asset::class => Asset::find()
                ->limit(1)
                ->inRandomOrder()
                ->one()->id,
            \CraftCms\Cms\Field\LinkTypes\Email::class => $this->factory->email(),
            \CraftCms\Cms\Field\LinkTypes\Phone::class => $this->factory->phoneNumber(),
            \CraftCms\Cms\Field\LinkTypes\Url::class => $this->factory->url(),
        };

        return [
            'type'   => $type::id(),
            'value'  => $value,
            'target' => $this->factory->randomElement(['', '_blank']),
            'title'  => random_int(0, 1) === 1 ? $this->factory->words(3, true) : null,
        ];
    }
}