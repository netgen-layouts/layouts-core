<?php

namespace Netgen\BlockManager\Tests\Configuration;

use Netgen\BlockManager\Configuration\ContainerConfiguration;

class ContainerConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameter()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(true));

        $configuration = new ContainerConfiguration();
        $configuration->setContainer($container);
        self::assertEquals(true, $configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $configuration = new ContainerConfiguration();
        $configuration->setContainer($container);
        self::assertEquals(false, $configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameter()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('getParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue('some_param_value'));

        $configuration = new ContainerConfiguration();
        $configuration->setContainer($container);
        self::assertEquals('some_param_value', $configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\ContainerConfiguration::getParameter
     * @expectedException \InvalidArgumentException
     */
    public function testGetParameterThrowsInvalidArgumentException()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $configuration = new ContainerConfiguration();
        $configuration->setContainer($container);
        $configuration->getParameter('some_param');
    }
}
