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

namespace anubarak\seeder\services\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;

class TableMaker extends BaseField
{
    /**
     * @param \secondred\tablemaker\fields\TableMakerField $field
     *
     * @inheritDoc
     */
    public function generate(FieldInterface $field, ElementInterface $element = null)
    {
        $colNr = $this->factory->numberBetween(3,5);
        $rowNr = $this->factory->numberBetween(5,15);
        $columns = [];
        $rows = [];

        for($i = 0; $i < $colNr; $i++){
            $columns["col{$i}"] = [
                'heading' => $this->factory->words($this->factory->numberBetween(1,3), true),
                'width' => '',
                'align' => $this->factory->randomElement(['left', 'center', 'right']),
                'style' => $this->factory->randomElement(['', 'bold', 'italic', 'underline']),
                'options' => '',
            ];
        }

        for($i = 0; $i < $rowNr; $i++){
            $rows["row{$i}"] = [];
            for($j = 0; $j < $colNr; $j++){
                $rows["row{$i}"]["col{$j}"] = $this->factory->words($this->factory->numberBetween(1,3), true);
            }
        }

        return [
            'markFirst'       => $this->factory->boolean(),
            'hideFirstColumn' => $this->factory->boolean(),
            'columns'         => $columns,
            'rows'            => $rows,
        ];
    }
}