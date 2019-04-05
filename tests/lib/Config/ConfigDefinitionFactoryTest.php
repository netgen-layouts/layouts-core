<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\Config\ConfigDefinitionFactory;
use Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderFactoryInterface;
use Netgen\BlockManager\Parameters\ParameterBuilderInterface;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionFactoryTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $handlerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $parameterBuilderFactoryMock;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionFactory
     */
    private $factory;

    public function setUp(): void
    {
        $this->parameterBuilderFactoryMock = $this->createMock(ParameterBuilderFactoryInterface::class);
        $this->parameterBuilderFactoryMock
            ->expects(self::any())
            ->method('createParameterBuilder')
            ->willReturn($this->createMock(ParameterBuilderInterface::class));

        $this->factory = new ConfigDefinitionFactory($this->parameterBuilderFactoryMock);
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::__construct
     * @covers \Netgen\BlockManager\Config\ConfigDefinitionFactory::buildConfigDefinition
     */
    public function testBuildConfigDefinition(): void
    {
        $this->handlerMock = $this->createMock(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'definition',
            $this->handlerMock
        );

        self::assertSame('definition', $configDefinition->getConfigKey());
    }
}
