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

namespace Anubarak\Seeder\Seeder\Fields;

use Anubarak\Seeder\SeederServiceProvider;
use CraftCms\Cms\Element\Contracts\ElementInterface;
use CraftCms\Cms\Field\Contracts\FieldInterface;

/**
 * Class PlainText
 *
 * @package Anubarak\Seeder\Seeder\fields
 * @since   19/12/2023
 * @author  by Robin Schambach
 */
class PlainText extends BaseField
{
    /**
     * @inheritDoc
     */
    public function generate(\CraftCms\Cms\Field\PlainText|FieldInterface $field, ElementInterface|null $element = null)
    {
        if (SeederServiceProvider::getInstance()->getSettings()->isGuessFieldContentByHandle()) {
            $value = $this->guessFieldContentByHandle($field);
            if ($value) {
                return $value;
            }
        }

        if (!$field->multiline) {
            return $this->factory->text($field->charLimit ?: 200);
        }

        return $this->factory->realText($field->charLimit ?: 200);
    }

    /**
     * guessFieldContentByHandle
     *
     * @param \CraftCms\Cms\Field\PlainText $field
     *
     * @return string|null
     * @author Robin Schambach
     * @since  15.07.2024
     */
    public function guessFieldContentByHandle(\CraftCms\Cms\Field\PlainText $field): ?string
    {
        switch ($field->handle) {
            case 'organization':
                return $this->factory->company();
                break;
            case 'newPassword':
            case 'currentPassword':
            case 'password2':
            case 'password':
                return $this->factory->password();
                break;
            case 'phoneNumber':
            case 'phone':
            case 'telefon':
                return $this->factory->phoneNumber();
                break;
            case 'fullName':
                return $this->factory->firstName() . ' ' . $this->factory->lastName();
                break;
            case 'name':
            case 'lastName':
            case 'surName':
                return $this->factory->lastName();
                break;
            case 'firstName':
                return $this->factory->firstName();
                break;
            case 'mail':
            case 'email':
            case 'e-mail':
                return $this->factory->email();
                break;
            case 'plz':
            case 'zip':
            case 'zipCode':
            case 'postalCode':
                return $this->factory->postcode();
                break;
            case 'location':
            case 'locality':
            case 'city':
                return $this->factory->city();
                break;
            case 'addressLine1':
                return $this->factory->streetAddress();
                break;
            case 'street':
                return $this->factory->streetName();
                break;
            case 'houseNumber':
                return $this->factory->buildingNumber();
                break;
        }

        return null;
    }
}