<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\DynamicContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

class DynamicContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Netgen\BlockManager\Block\ContainerDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handler = new DynamicContainerDefinitionHandler();

        $this->configMock = $this->createMock(Configuration::class);

        $this->blockDefinition = new ContainerDefinition(
            array(
                'identifier' => 'block_definition',
                'handler' => $this->handler,
                'config' => $this->configMock,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::isDynamicContainer
     */
    public function testIsDynamicContainer()
    {
        $this->assertTrue($this->blockDefinition->isDynamicContainer());
    }
}
