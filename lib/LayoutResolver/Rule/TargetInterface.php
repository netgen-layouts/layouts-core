<?php

namespace Netgen\BlockManager\LayoutResolver\Rule;

interface TargetInterface
{
    /**
     * Sets the values of this target.
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

    /**
     * Returns if this target matches.
     *
     * @return bool
     */
    public function matches();
}
