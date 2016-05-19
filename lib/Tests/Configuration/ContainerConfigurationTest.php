<?php

namespace Netgen\BlockManager\Tests\Configuration;

use Netgen\BlockManager\Configuration\ContainerConfiguration;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerConfigurationTest extends \PHPUnit_Framework_TestCase
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
        $this->containerMock = $this->getMock(ContainerInterface::class);
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

        self::assertTrue($this->configuration->hasParameter('some_param'));
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

        self::assertFalse($this->configuration->hasParameter('some_param'));
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

        self::assertEquals('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::getParameter
     * @expectedException \OutOfBoundsException
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
