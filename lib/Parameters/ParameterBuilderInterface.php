<?php

namespace Netgen\BlockManager\Parameters;

use Countable;

interface ParameterBuilderInterface extends Countable
{
    /**
     * Adds the parameter to the builder.
     *
     * @param string $name
     * @param string $type
     * @param array $options
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function add($name, $type, array $options = array());

    /**
     * Returns the builder for parameter with provided name.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function get($name);

    /**
     * Returns if the builder has the parameter with provided name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name);

    /**
     * Removes the parameter from the builder.
     *
     * @param string $name
     *
     * @return \Netgen\BlockManager\Parameters\ParameterBuilderInterface
     */
    public function remove($name);

    /**
     * Returns the count of the parameters.
     *
     * @return int
     */
    public function count();

    /**
     * Builds the parameters.
     *
     * @return \Netgen\BlockManager\Parameters\Parameter[]
     */
    public function buildParameters();
}
