<?php

namespace Netgen\BlockManager\Tests\Configuration\Factory;

use Netgen\BlockManager\Configuration\ContainerType\ContainerType;
use Netgen\BlockManager\Configuration\Factory\ContainerTypeFactory;
use Netgen\BlockManager\Tests\Layout\Container\Stubs\ContainerDefinition;
use PHPUnit\Framework\TestCase;

class ContainerTypeFactoryTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Factory\ContainerTypeFactory::buildContainerType
     */
    public function testBuildContainerType()
    {
        $containerDefinition = new ContainerDefinition('container');

        $containerType = ContainerTypeFactory::buildContainerType(
            'container',
            array(
                'name' => 'Container',
                'definition_identifier' => 'container',
                'defaults' => array(
                    'viewType' => 'default',
                ),
            ),
            $containerDefinition
        );

        $this->assertEquals(
            new ContainerType(
                array(
                    'identifier' => 'container',
                    'name' => 'Container',
                    'containerDefinition' => $containerDefinition,
                    'defaults' => array(
                        'viewType' => 'default',
                    ),
                )
            ),
            $containerType
        );
    }
}
