<?php

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\elements\Asset;
use craft\elements\Entry;

class CraftLink extends BaseField
{
    /**
     * @param \craft\fields\Link $field
     *
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {

        $type = $this->factory->randomElement($field->getLinkTypes());
        $value = match ($type::class) {
            \craft\fields\linktypes\Entry::class => Entry::find()
                ->limit(1)
                ->orderBy(\anubarak\seeder\helpers\DB::random())
                ->one()->id,
            \craft\fields\linktypes\Asset::class => Asset::find()
                ->limit(1)
                ->orderBy(\anubarak\seeder\helpers\DB::random())
                ->one()->id,
            \craft\fields\linktypes\Email::class => $this->factory->email(),
            \craft\fields\linktypes\Phone::class => $this->factory->phoneNumber(),
            \craft\fields\linktypes\Url::class => $this->factory->url(),
        };

        return [
            'type' => $type::id(),
            'value' => $value,
            'target' => $this->factory->randomElement(['', '_blank']),
            'title' => random_int(0, 1) === 1? $this->factory->words(3, true) : null,
        ];
    }
}