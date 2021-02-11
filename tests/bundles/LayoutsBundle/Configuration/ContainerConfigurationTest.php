<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Configuration;

use Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration;
use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerConfigurationTest extends TestCase
{
    private MockObject $containerMock;

    private ContainerConfiguration $configuration;

    protected function setUp(): void
    {
        $this->createConfiguration();
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameter(): void
    {
        $this->containerMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(true);

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'some_value']);

        $this->containerMock
            ->expects(self::never())
            ->method('hasParameter');

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::hasParameter
     */
    public function testHasParameterWithNoParameter(): void
    {
        $this->containerMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(false);

        self::assertFalse($this->configuration->hasParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameter(): void
    {
        $this->containerMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(true);

        $this->containerMock
            ->expects(self::once())
            ->method('getParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn('some_param_value');

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'injected']);

        $this->containerMock
            ->expects(self::never())
            ->method('hasParameter');

        $this->containerMock
            ->expects(self::never())
            ->method('getParameter');

        self::assertSame('injected', $this->configuration->getParameter('some_param'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Configuration\ContainerConfiguration::getParameter
     */
    public function testGetParameterThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Parameter "some_param" does not exist in configuration.');

        $this->containerMock
            ->expects(self::once())
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(false);

        $this->configuration->getParameter('some_param');
    }

    /**
     * @param array<string, mixed> $injectedParameters
     */
    private function createConfiguration(array $injectedParameters = []): void
    {
        $this->containerMock = $this->createMock(ContainerInterface::class);
        $this->configuration = new ContainerConfiguration($this->containerMock, $injectedParameters);
    }
}
