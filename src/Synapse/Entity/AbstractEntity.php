<?php

namespace Synapse\Entity;

use InvalidArgumentException;
use Synapse\Stdlib\Arr;

abstract class AbstractEntity
{
    protected $object = [];

    protected $rules = [];

    /**
     * Handle magic getters and setters
     *
     * @param  string $method Name of the method called
     * @param  array $args    Arguments passed to the method
     * @return mixed
     */
    public function __call($method, array $args)
    {
        // If the method name is less than or equal to four characters
        // then it's not a getter or a setter
        if (strlen($method) <= 4) {
            throw new InvalidArgumentException('Method not found');
        }

        // Whether we are setting or getting
        $type = substr($method, 0, 3);

        if ($type !== 'get' and $type !== 'set') {
            throw new InvalidArgumentException('Method not found');
        }

        // Get the property name
        $property = lcfirst(substr($method, 3));

        // Make sure the property
        if (! array_key_exists($property, $this->object)) {
            throw new InvalidArgumentException('Property, '.$property.', not found');
        }

        if ($type === 'get') {
            // Return the property
            return $this->object[$property];
        } elseif ($type === 'set') {
            // Check the rules
            if (array_key_exists($property, $this->rules)) {
                // Run the rules
                if (! $this->processRules($property, $args[0])) {
                    throw new InvalidArgumentException('Invalid value for '.$property);
                }
            }

            // Set the property
            $this->object[$property] = $args[0];

            // Fluent interface
            return $this;
        }
    }

    /**
     * Get all columns of this entity
     *
     * @return [type] [description]
     */
    public function getColumns()
    {
        return array_keys($this->object);
    }

    /**
     * Get values which are saved to the database.
     *
     * Useful if as_array is overridden to return values not
     * saved to the database.
     *
     * @return array
     */
    public function getDbValues()
    {
        return Arr::extract($this->asArray(), $this->getColumns());
    }

    public function asArray()
    {
        return $this->object;
    }

    public function fromArray(array $values)
    {
        foreach ($this->object as $key => $value) {
            if (array_key_exists($key, $values)) {
                $setter = 'set'.ucfirst($key);
                $this->$setter($values[$key]);
            }
        }

        return $this;
    }

    public function isNew()
    {
        return $this->getId() ? false : true;
    }

    /**
     * Runs the class's set validation rules given a property name and a value
     *
     * @param  string $property the property name
     * @param  mixed  $value    the value to check
     * @return boolean
     */
    protected function processRules($property, $value)
    {
        if (! array_key_exists($property, $this->rules)) {
            return true;
        }

        $rules = $this->rules[$property];

        $validation = Validation::factory([
            $property => $value
        ]);

        $validation->rules($property, $rules);

        return $validation->check();
    }
}
