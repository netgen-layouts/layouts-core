<?php

namespace Netgen\BlockManager\Tests\Configuration\Stubs;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType as BaseContainerType;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinition;

class ContainerType extends BaseContainerType
{
    /**
     * Constructor.
     *
     * @param array $properties
     */
    public function __construct(array $properties = array())
    {
        $properties['name'] = $properties['identifier'];
        $properties['containerDefinition'] = new ContainerDefinition($properties['identifier']);

        parent::__construct($properties);
    }
}
