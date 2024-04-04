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
use craft\elements\Entry;

/**
 * Class EntryConfig
 *
 * @package anubarak\seeder\models
 * @since   20/12/2023
 * @author  by Robin Schambach
 */
class EntryConfig extends ElementConfig
{
    /**
     * The desired section handle
     *
     * @var string|null $section
     */
    protected string|null $section = null;
    /**
     * the desired entry type handle
     *
     * @var string|null $entryType
     */
    protected string|null $entryType = null;

    /**
     * @param string|null $entryType
     */
    public function __construct(string $entryType = null, array $fieldConfig = [])
    {
        $this->entryType = $entryType;
        $this->fieldConfig = $fieldConfig;

        parent::__construct(Entry::class, $fieldConfig);
    }

    /**
     * @return string|null
     */
    public function getEntryType(): ?string
    {
        return $this->entryType;
    }

    /**
     * @param string|null $entryType
     *
     * @return EntryConfig
     */
    public function setEntryType(?string $entryType): EntryConfig
    {
        $this->entryType = $entryType;

        return $this;
    }

    /**
     * matchElement
     *
     * @param \craft\elements\Entry|\craft\base\ElementInterface $element
     *
     * @return bool
     * @throws \yii\base\InvalidConfigException
     * @author Robin Schambach
     * @since  20/12/2023
     */
    protected function matchElement(Entry|ElementInterface $element): bool
    {
        return $element->getType()->handle === $this->entryType;
    }
}