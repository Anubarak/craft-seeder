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

namespace anubarak\seeder\services\unique;
use craft\base\Field;

/**
 * Interface for unique fields
 */
interface UniqueFieldInterface
{
    /**
     * a description that is shown in the CP what values this field might contain
     *
     * @param \craft\base\Field $field
     *
     * @return string
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getDescription(Field $field): string;

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
     * @param \craft\base\Field $field
     *
     * @return array
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getValues(Field $field): array;

    /**
     * A class to reference this field correctly
     *
     * @return string
     * @author Robin Schambach
     * @since  10.07.2024
     */
    public function getFieldClass(): string;
}