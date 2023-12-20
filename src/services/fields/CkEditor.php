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

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use Faker\Provider\Lorem;

/**
 * Fields Service
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class CkEditor extends BaseField  {

    /**
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        return Lorem::sentences(rand(5, 20), true);
    }
}