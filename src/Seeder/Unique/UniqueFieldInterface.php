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

namespace Anubarak\Seeder\Seeder\Unique;

use CraftCms\Cms\Field\Contracts\FieldInterface;

/**
 * Interface for unique fields
 */
interface UniqueFieldInterface
{
    /**
     * a description that is shown in the CP what values this field might contain
     *
     * @param FieldInterface $field
     *
     * @return string
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getDescription(FieldInterface $field): string;

    /**
     * get all possible values, this can be an array by scalar values or callbacks
     * [
     *  true,
     *  false,
     * ]
     *
     * [
     *  'a',
     *  'b',
     *  'c',
     * ]
     *
     * [
     *  null,
     *  fn() => Seeder::$plugin->fields->Title()
     * ]
     *
     * @param FieldInterface $field
     *
     * @return array
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getValues(FieldInterface $field): array;

    /**
     * A class to reference this field correctly
     *
     * @return string
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getFieldClass(): string;
}