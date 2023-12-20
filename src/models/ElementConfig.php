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

namespace anubarak\seeder\models;

use craft\base\ElementInterface;

abstract class ElementConfig
{
    /**
     * @var \anubarak\seeder\models\FieldCallback[] $fieldConfig
     */
    protected array $fieldConfig = [];

    /**
     * @param string $elementType
     * @param array  $fieldConfig
     */
    public function __construct(protected string $elementType, array $fieldConfig)
    {
        $this->fieldConfig = $fieldConfig;
    }

    /**
     * getElementType
     *
     * @return string
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function getElementType(): string
    {
        return $this->elementType;
    }

    /**
     * match
     *
     * @param \craft\base\ElementInterface $element
     *
     * @return bool
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function match(ElementInterface $element): bool
    {
        return get_class($element) === $this->elementType && $this->matchElement($element);
    }

    /**
     * getFieldConfig
     *
     * @return array|\anubarak\seeder\models\FieldCallback[]
     * @author Robin Schambach
     * @since  20/12/2023
     */
    public function getFieldConfig(): array
    {
        return $this->fieldConfig;
    }

    protected abstract function matchElement(ElementInterface $element): bool;
}