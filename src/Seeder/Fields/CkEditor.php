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

use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;
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
    public function generate(FieldInterface $field, ElementInterface|null $element = null)
    {
        return Lorem::sentences(rand(5, 20), true);
    }
}