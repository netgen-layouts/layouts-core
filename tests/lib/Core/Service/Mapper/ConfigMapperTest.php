<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

final class ConfigMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    private $configDefinition;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    private $mapper;

    public function setUp()
    {
        $this->configDefinition = new ConfigDefinition('config_key');

        $this->mapper = new ConfigMapper(new ParameterMapper());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::mapConfig
     */
    public function testMapConfig()
    {
        $mappedConfig = $this->mapper->mapConfig(
            [
                'config_key' => [
                    'param' => 'value',
                ],
            ],
            [
                'config_key' => $this->configDefinition,
            ]
        );

        $this->assertInternalType('array', $mappedConfig);
        $this->assertArrayHasKey('config_key', $mappedConfig);

        $config = $mappedConfig['config_key'];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('config_key', $config->getConfigKey());
        $this->assertEquals($this->configDefinition, $config->getDefinition());

        $this->assertTrue($config->hasParameter('param'));

        $this->assertInstanceOf(Parameter::class, $config->getParameter('param'));

        $this->assertEquals('value', $config->getParameter('param')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::serializeValues
     */
    public function testSerializeValues()
    {
        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('param', 'new_value');

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
                ],
            ]
        );

        $this->assertEquals(
            [
                'config_key' => [
                    'param' => 'new_value',
                    'param2' => 'value2',
                ],
            ],
            $serializedConfig
        );
    }
}
