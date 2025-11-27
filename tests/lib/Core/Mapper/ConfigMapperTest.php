<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Core\Mapper\ConfigMapper;
use Netgen\Layouts\Core\Mapper\ParameterMapper;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigMapper::class)]
final class ConfigMapperTest extends TestCase
{
    private ConfigDefinition $configDefinition;

    private ConfigMapper $mapper;

    protected function setUp(): void
    {
        $handler = new ConfigDefinitionHandler();

        $this->configDefinition = ConfigDefinition::fromArray(
            [
                'parameterDefinitions' => $handler->getParameterDefinitions(),
            ],
        );

        $this->mapper = new ConfigMapper(new ParameterMapper());
    }

    public function testMapConfig(): void
    {
        $mappedConfig = $this->mapper->mapConfig(
            [
                'config_key' => [
                    'param' => 'value',
                ],
            ],
            [
                'config_key' => $this->configDefinition,
            ],
        );

        $mappedConfig = [...$mappedConfig];

        self::assertArrayHasKey('config_key', $mappedConfig);
        self::assertContainsOnlyInstancesOf(Config::class, $mappedConfig);

        /** @var \Netgen\Layouts\API\Values\Config\Config $config */
        $config = $mappedConfig['config_key'];

        self::assertSame('config_key', $config->configKey);
        self::assertSame($this->configDefinition, $config->definition);

        self::assertTrue($config->hasParameter('param'));
        self::assertSame('value', $config->getParameter('param')->value);
    }

    public function testSerializeValues(): void
    {
        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param', 'new_value');
        $configStruct->setParameterValue('param2', 'new_value2');

        $serializedConfig = $this->mapper->serializeValues(
            [
                'config_key' => $configStruct,
            ],
            [
                'config_key' => $this->configDefinition,
            ],
            [
                'config_key' => [
                    'param2' => 'value2',
                    'param3' => 'value3',
                ],
            ],
        );

        self::assertSame(
            [
                'config_key' => [
                    'param2' => 'new_value2',
                    'param3' => 'value3',
                    'param' => 'new_value',
                ],
            ],
            [...$serializedConfig],
        );
    }
}
