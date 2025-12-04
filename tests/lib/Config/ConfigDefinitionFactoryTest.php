<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinitionFactory;
use Netgen\Layouts\Config\ConfigDefinitionHandlerInterface;
use Netgen\Layouts\Parameters\ParameterBuilderFactory;
use Netgen\Layouts\Parameters\Registry\ParameterTypeRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigDefinitionFactory::class)]
final class ConfigDefinitionFactoryTest extends TestCase
{
    private ConfigDefinitionFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ConfigDefinitionFactory(new ParameterBuilderFactory(new ParameterTypeRegistry([])));
    }

    public function testBuildConfigDefinition(): void
    {
        $handlerStub = self::createStub(ConfigDefinitionHandlerInterface::class);

        $configDefinition = $this->factory->buildConfigDefinition(
            'definition',
            $handlerStub,
        );

        self::assertSame('definition', $configDefinition->configKey);
    }
}
