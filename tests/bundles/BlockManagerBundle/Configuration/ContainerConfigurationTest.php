<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Configuration;

use Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerConfigurationTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $containerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration
     */
    private $configuration;

    public function setUp(): void
    {
        $this->createConfiguration();
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameter(): void
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(true));

        $this->assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'some_value']);

        $this->containerMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter(): void
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $this->assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameter(): void
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
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'injected']);

        $this->containerMock
            ->expects($this->never())
            ->method('hasParameter');

        $this->containerMock
            ->expects($this->never())
            ->method('getParameter');

        $this->assertEquals('injected', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Configuration\ContainerConfiguration::getParameter
     * @expectedException \Netgen\Bundle\BlockManagerBundle\Exception\ConfigurationException
     * @expectedExceptionMessage Parameter "some_param" does not exist in configuration.
     */
    public function testGetParameterThrowsConfigurationException(): void
    {
        $this->containerMock
            ->expects($this->once())
            ->method('hasParameter')
            ->with($this->equalTo('netgen_block_manager.some_param'))
            ->will($this->returnValue(false));

        $this->configuration->getParameter('some_param');
    }

    private function createConfiguration(array $injectedParameters = []): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->configuration = new ContainerConfiguration($this->containerMock, $injectedParameters);
    }
}
