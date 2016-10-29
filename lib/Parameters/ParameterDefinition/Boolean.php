<?php

namespace Netgen\BlockManager\Parameters\ParameterDefinition;

use Netgen\BlockManager\Parameters\ParameterDefinition;

class Boolean extends ParameterDefinition
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'boolean';
    }

    /**
     * Returns the default parameter value.
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        if ($this->isRequired && $this->defaultValue === null) {
            return false;
        }

        return parent::getDefaultValue();
    }
}
