<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\services\fields;

use craft\base\Component;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use Faker\Factory;
use Faker\Provider\Base;
use Faker\Provider\Lorem;

/**
 * Class Redactor
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Redactor extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        return Lorem::sentences(rand(5, 20), true);
    }
}