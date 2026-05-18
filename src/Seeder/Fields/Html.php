<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace Anubarak\Seeder\Seeder\Fields;

use craft\base\Component;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
use Faker\Factory;
use Faker\Provider\Base;
use Faker\Provider\Lorem;

/**
 * Class Redactor
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Html extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface|null $element = null)
    {
        return \CraftCms\Cms\Support\Html::tag('p', Lorem::sentences(rand(5, 20), true));
    }
}