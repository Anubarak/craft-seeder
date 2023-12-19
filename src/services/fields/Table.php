<?php
/**
 * Craft CMS Plugins
 *
 * Created with PhpStorm.
 *
 * @link      https://github.com/Anubarak/
 * @email     anubarak1993@gmail.com
 * @copyright Copyright (c) 2023 Robin Schambach|Secondred Newmedia GmbH
 */

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;

/**
 * Class Table
 *
 * @package anubarak\seeder\services\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class Table extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\craft\fields\Table|FieldInterface $field, ElementInterface $element)
    {
        if ($field->minRows) {
            $min = $field->minRows;
        } else {
            $min = 1;
        }
        if ($field->maxRows) {
            $max = $field->maxRows;
        } else {
            $max = $min + 10;
        }

        $table = [];
        for ($x = 0, $xMax = random_int($min, $max); $x <= $xMax; $x++) {
            foreach ($field->columns as $handle => $col) {
                switch ($col['type']) {
                    case 'singleline':
                        $table[$x][$handle] = $this->factory->text(30);
                        break;
                    case 'multiline':
                        $table[$x][$handle] = $this->factory->realText(150, random_int(2, 5));
                        break;
                    case 'checkbox':
                    case 'lightswitch':
                        $table[$x][$handle] = $this->factory->boolean;
                        break;
                    case 'number':
                        $table[$x][$handle] = $this->factory->numberBetween(2, 30);
                        break;
                    case 'time':
                    case 'date':
                        $table[$x][$handle] = $this->factory->dateTime;
                        break;
                    case 'color':
                        $table[$x][$handle] = $this->factory->hexColor;
                        break;
                }
            }
        }

        return $table;
    }
}