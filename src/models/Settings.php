<?php
/**
 * Seeder plugin for Craft CMS 3.x
 *
 * Entries seeder for Craft CMS
 *
 * @link      https://studioespresso.co
 * @copyright Copyright (c) 2018 Studio Espresso
 */

namespace anubarak\seeder\models;

use craft\config\BaseConfig;

/**
 * Seeder Settings Model
 *
 * This is a model used to define the plugin's settings.
 *
 * Models are containers for data. Just about every time information is passed
 * between services, controllers, and templates in Craft, it’s passed via a model.
 *
 * https://craftcms.com/docs/plugins/models
 *
 * @author    Studio Espresso
 * @package   Seeder
 * @since     1.0.0
 */
class Settings extends BaseConfig
{
    // Public Properties
    // =========================================================================

    public $sets;
    public $debug = false;
    public $eachMatrixBlock = false;
    public $useLocalAssets = false;
    /**
     * Config to provide additional information/options for certain fields in certain layouts/ scenarios
     * it's indexed by element type
     * [
     *      User::class => [
     *          'fieldHandle' => [ options ]
     *      ]
     *      Entry::class => [
     *          'sectionHandle' => [
     *              'fieldHandle' => [ options ]
     *          ]
     *      ]
     *      Category::class => [
     *          'groupHandle' => [
     *              'fieldHandle => [options]
     *      ]
     * ]
     *
     *     'fieldsConfig' => [
     *          User::class => [
     *              'phone'        => 'phoneNumber',
     *              'street'       => 'streetName',
     *              'zip'          => 'postcode',
     *              'location'     => 'city',
     *              'country'      => static function() {
     *                  return 'DE';
     *              },
     *              'houseNumber'  => 'buildingNumber',
     *              'textarea'     => static function() {
     *                  return '';
     *              },
     *          }
     *      ]
     *
     * @var array $fieldsConfig
     */
    public $fieldsConfig = [];
    /**
     * The language it should use
     *
     * @var string $fakerProvider
     */
    public $fakerProvider = 'de_DE';
}
