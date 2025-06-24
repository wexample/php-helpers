<?php

namespace Wexample\Helpers\Class;

abstract class AbstractFromArrayObject
{

    /**
     * Constructor that can take an object or array and dynamically assign properties
     *
     * @param array|object $data Data to assign to properties
     */
    public function __construct(array|object $data = [])
    {
        // Convert object to array if needed
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        // Assign properties dynamically
        foreach (static::getAllowedProperties() as $property) {
            if (isset($data[$property])) {
                $this->$property = $data[$property];
            }
        }
    }

    /**
     * Properties that can be dynamically assigned from data
     */
    abstract public static function getAllowedProperties(): array;
}