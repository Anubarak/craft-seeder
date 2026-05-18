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
use CraftCms\Cms\Field\Contracts\FieldInterface;
use Illuminate\Support\Facades\App;

/**
 * Class Money
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   12.07.2024
 * @author  by Robin Schambach
 */
class Money extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface|null $element = null)
    {
        return [
            'value'  => $this->factory->numberBetween(100, 100000),
            'locale' => App::getLocale()
        ];
    }
}