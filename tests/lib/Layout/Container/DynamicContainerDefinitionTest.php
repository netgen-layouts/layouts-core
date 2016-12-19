<?php

namespace Netgen\BlockManager\Tests\Layout\Container;

use Netgen\BlockManager\Layout\Container\DynamicContainerDefinition;
use Netgen\BlockManager\Layout\Container\PlaceholderDefinition;
use PHPUnit\Framework\TestCase;

class DynamicContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\DynamicContainerDefinition
     */
    protected $containerDefinition;

    public function setUp()
    {
        $this->containerDefinition = new DynamicContainerDefinition(
            array(
                'dynamicPlaceholder' => new PlaceholderDefinition(array('identifier' => 'dynamic')),
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Layout\Container\DynamicContainerDefinition::getDynamicPlaceholder
     */
    public function testGetDynamicPlaceholder()
    {
        $this->assertEquals(
            new PlaceholderDefinition(array('identifier' => 'dynamic')),
            $this->containerDefinition->getDynamicPlaceholder()
        );
    }
}
