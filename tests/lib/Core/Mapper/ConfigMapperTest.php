<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Core\Mapper;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Core\Mapper\ConfigMapper;
use Netgen\Layouts\Core\Mapper\ParameterMapper;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

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

    /**
     * @covers \Netgen\Layouts\Core\Mapper\ConfigMapper::__construct
     * @covers \Netgen\Layouts\Core\Mapper\ConfigMapper::mapConfig
     */
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

        $mappedConfig = iterator_to_array($mappedConfig);

        self::assertArrayHasKey('config_key', $mappedConfig);
        self::assertContainsOnlyInstancesOf(Config::class, $mappedConfig);

        $config = $mappedConfig['config_key'];
        self::assertSame('config_key', $config->getConfigKey());
        self::assertSame($this->configDefinition, $config->getDefinition());

        self::assertTrue($config->hasParameter('param'));
        self::assertSame('value', $config->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\Layouts\Core\Mapper\ConfigMapper::serializeValues
     */
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
            iterator_to_array($serializedConfig),
        );
    }
}
