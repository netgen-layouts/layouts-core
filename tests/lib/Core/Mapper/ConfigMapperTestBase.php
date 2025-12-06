<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use Netgen\Layouts\Tests\Core\CoreTestCase;

abstract class ConfigMapperTestBase extends CoreTestCase
{
    private ConfigDefinition $configDefinition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configDefinition = ConfigDefinition::fromArray(
            [
                'parameterDefinitions' => new ConfigDefinitionHandler()->getParameterDefinitions(),
            ],
        );
    }

    public function testMapConfig(): void
    {
        $mappedConfig = $this->configMapper->mapConfig(
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

        $serializedConfig = $this->configMapper->serializeValues(
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
