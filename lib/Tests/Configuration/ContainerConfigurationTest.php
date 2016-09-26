<?php

namespace Netgen\BlockManager\Tests\Configuration;

use Netgen\BlockManager\Configuration\ContainerConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ContainerConfigurationTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * @var \Netgen\BlockManager\Configuration\ContainerConfiguration
     */
    protected $configuration;

    public function setUp()
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->configuration = new ContainerConfiguration();
        $this->configuration->setContainer($this->containerMock);
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameter()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(true));

        $this->assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $this->assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameter()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(true));

        $this->containerMock
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue('some_param_value'));

        $this->assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::getParameter
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     */
    public function testGetParameterThrowsOutOfBoundsException()
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $this->configuration->getParameter('some_param');
    }
}
