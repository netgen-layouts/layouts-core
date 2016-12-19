<?php

namespace Netgen\BlockManager\Configuration\ContainerType;

use Netgen\BlockManager\ValueObject;

class ContainerType extends ValueObject
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    protected $containerDefinition;

    /**
     * @var array
     */
    protected $defaults = array();

    /**
     * Returns the container type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the container type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the container definition.
     *
     * @return \Netgen\BlockManager\Layout\Container\ContainerDefinitionInterface
     */
    public function getContainerDefinition()
    {
        return $this->containerDefinition;
    }

    /**
     * Returns the default container values.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns the default container name.
     *
     * @return string
     */
    public function getDefaultName()
    {
        return isset($this->defaults['name']) ? $this->defaults['name'] : '';
    }

    /**
     * Returns the default container view type.
     *
     * @return string
     */
    public function getDefaultViewType()
    {
        return isset($this->defaults['view_type']) ? $this->defaults['view_type'] : '';
    }

    /**
     * Returns the default container parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return isset($this->defaults['parameters']) ? $this->defaults['parameters'] : array();
    }
}
