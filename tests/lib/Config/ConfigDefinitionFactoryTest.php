<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactoryInterface;
use Netgen\Layouts\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionFactoryTest extends TestCase
{
    private MockObject $handlerMock;

    private MockObject $parameterBuilderFactoryMock;

    private ConfigDefinitionFactory $factory;

    protected function setUp(): void
    {
        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->method('createParameterBuilder')
            ->willReturn($this->createMock(ParameterBuilderInterface::class));

        $this->factory = new ConfigDefinitionFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\Layouts\Config\ConfigDefinitionFactory::__construct
     * @covers \Netgen\Layouts\Config\ConfigDefinitionFactory::buildConfigDefinition
     */
    public function testBuildConfigDefinition(): void
    {
        $this->handlerMock = $this->createMock(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'definition',
            $this->handlerMock,
        );

        self::assertSame('definition', $configDefinition->getConfigKey());
    }
}
