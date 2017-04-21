<?php

namespace Jivochat\Webhooks;

/**
 * Populates object properties via given array.
 */
trait PopulateObjectViaArray
{
    /**
     * Populates (massively sets) object properties via given associative array of values.
     *
     * If object has a setter method for concrete property, the setter will be executed for populating this property.
     * Otherwise, the property will be set directly.
     *
     * @param array $data Associative array with object data to be populated.
     * Array keys must represent property names, array values - properties value.
     */
    public function populate(array $data): void
    {
        foreach ($data as $name => $value) {
            if (!property_exists(__CLASS__, $name)) {
                continue;
            }

            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
                continue;
            }

            $this->$name = $value;
        }
    }
}