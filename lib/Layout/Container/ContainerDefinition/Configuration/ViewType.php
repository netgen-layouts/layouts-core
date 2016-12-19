<?php

namespace Netgen\BlockManager\Layout\Container\ContainerDefinition\Configuration;

use Netgen\BlockManager\ValueObject;

class ViewType extends ValueObject
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
     * Returns the view type identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns the view type name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
