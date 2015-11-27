<?php

namespace Netgen\BlockManager\LayoutResolver;

interface TargetInterface
{
    /**
     * Returns the unique identifier of the target.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Sets the values to the target.
     *
     * @param array $values
     */
    public function setValues(array $values);

    /**
     * Returns the values from the target.
     *
     * @return array
     */
    public function getValues();
}
