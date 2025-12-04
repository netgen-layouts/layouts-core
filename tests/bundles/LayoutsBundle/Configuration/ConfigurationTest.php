<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Configuration;

use Netgen\Bundle\LayoutsBundle\Configuration\Configuration;
use Netgen\Bundle\LayoutsBundle\Exception\ConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[CoversClass(Configuration::class)]
final class ConfigurationTest extends TestCase
{
    private Stub&ContainerInterface $containerStub;

    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->createConfiguration();
    }

    public function testHasParameter(): void
    {
        $this->containerStub
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(true);

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    public function testHasParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'some_value']);

        self::assertTrue($this->configuration->hasParameter('some_param'));
    }

    public function testHasParameterWithNoParameter(): void
    {
        $this->containerStub
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(false);

        self::assertFalse($this->configuration->hasParameter('some_param'));
    }

    public function testGetParameter(): void
    {
        $this->containerStub
            ->method('hasParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn(true);

        $this->containerStub
            ->method('getParameter')
            ->with(self::identicalTo('netgen_layouts.some_param'))
            ->willReturn('some_param_value');

        self::assertSame('some_param_value', $this->configuration->getParameter('some_param'));
    }

    public function testGetParameterWithInjectedParameter(): void
    {
        $this->createConfiguration(['some_param' => 'injected']);

        self::assertSame('injected', $this->configuration->getParameter('some_param'));
    }

    public function testGetParameterThrowsConfigurationException(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Parameter "some_param" does not exist in configuration.');

        $this->containerStub
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
        $this->containerStub = self::createStub(ContainerInterface::class);
        $this->configuration = new Configuration($this->containerStub, $injectedParameters);
    }
}
