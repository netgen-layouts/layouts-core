<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigDefinitionFactory::class)]
final class ConfigDefinitionFactoryTest extends TestCase
{
    private ConfigDefinitionFactory $factory;

    protected function setUp(): void
    {
        $parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $parameterBuilderFactoryMock
            ->method('createParameterBuilder')
            ->willReturn($this->createMock(ParameterBuilderInterface::class));

        $this->factory = new ConfigDefinitionFactory($parameterBuilderFactoryMock);
    }

    public function testBuildConfigDefinition(): void
    {
        $handlerMock = $this->createMock(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'definition',
            $handlerMock,
        );

        self::assertSame('definition', $configDefinition->configKey);
    }
}
