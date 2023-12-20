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

/**
 * Class FieldCallback
 *
 * @package anubarak\seeder\models
 * @since   20/12/2023
 * @author  by Robin Schambach
 */
class FieldCallback
{
    /**
     * custom callback that is triggered
     *
     * @var \Closure|null $callable
     */
    protected \Closure|null $callable = null;
    /**
     * a simple method in faker that is called
     * eg: text, realText, number etc
     *
     * @var string|null $fakerMethod
     */
    protected string|null $fakerMethod = null;

    /**
     * @param string $handle
     */
    public function __construct(protected string $handle)
    {
    }

    /**
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @return \Closure|null
     */
    public function getCallable(): ?\Closure
    {
        return $this->callable;
    }

    /**
     * custom callback that is executed for this field
     * params are a Faker Generator, the Field and the Element
     *
     * @param \Closure|null $callable
     *
     * @return FieldCallback
     */
    public function setCallable(?\Closure $callable): FieldCallback
    {
        $this->callable = $callable;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFakerMethod(): ?string
    {
        return $this->fakerMethod;
    }

    /**
     * a property that can be called via Faker
     * -> $faker->text
     *
     * @param string|null $fakerMethod
     *
     * @return FieldCallback
     */
    public function setFakerMethod(?string $fakerMethod): FieldCallback
    {
        $this->fakerMethod = $fakerMethod;

        return $this;
    }
}