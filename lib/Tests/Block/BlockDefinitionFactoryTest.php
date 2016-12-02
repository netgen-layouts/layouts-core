<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandlerInterface;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Block\BlockDefinitionFactory;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

class BlockDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterBuilderMock;

    public function setUp()
    {
        $this->configMock = $this->createMock(Configuration::class);
        $this->parameterBuilderMock = $this->createMock(ParameterBuilderInterface::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinitionFactory::buildBlockDefinition
     */
    public function testBuildBlockDefinition()
    {
        $blockDefinition = BlockDefinitionFactory::buildBlockDefinition(
            'definition',
            $this->createMock(BlockDefinitionHandlerInterface::class),
            $this->configMock,
            $this->parameterBuilderMock
        );

        $this->assertInstanceOf(BlockDefinitionInterface::class, $blockDefinition);
        $this->assertEquals('definition', $blockDefinition->getIdentifier());
        $this->assertEquals($this->configMock, $blockDefinition->getConfig());
    }
}
