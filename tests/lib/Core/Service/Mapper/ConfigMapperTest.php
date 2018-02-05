<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
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

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->configDefinition = new ConfigDefinition('http_cache', new HttpCacheConfigHandler());

        $this->mapper = new ConfigMapper(new ParameterMapper());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::mapConfig
     */
    public function testMapConfig()
    {
        $mappedConfig = $this->mapper->mapConfig(
            array(
                'http_cache' => array(
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
                ),
            ),
            array(
                'http_cache' => $this->configDefinition,
            )
        );

        $this->assertInternalType('array', $mappedConfig);
        $this->assertArrayHasKey('http_cache', $mappedConfig);

        $config = $mappedConfig['http_cache'];

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('http_cache', $config->getConfigKey());
        $this->assertEquals($this->configDefinition, $config->getDefinition());

        $this->assertTrue($config->hasParameter('use_http_cache'));
        $this->assertTrue($config->hasParameter('shared_max_age'));

        $this->assertInstanceOf(Parameter::class, $config->getParameter('use_http_cache'));
        $this->assertInstanceOf(Parameter::class, $config->getParameter('shared_max_age'));

        $this->assertTrue($config->getParameter('use_http_cache')->getValue());
        $this->assertEquals(300, $config->getParameter('shared_max_age')->getValue());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::serializeValues
     */
    public function testSerializeValues()
    {
        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('use_http_cache', true);

        $serializedConfig = $this->mapper->serializeValues(
            array(
                'http_cache' => $configStruct,
            ),
            array(
                'http_cache' => $this->configDefinition,
            ),
            array(
                'http_cache' => array(
                    'shared_max_age' => 300,
                ),
            )
        );

        $this->assertEquals(
            array(
                'http_cache' => array(
                    'use_http_cache' => true,
                    'shared_max_age' => 300,
                ),
            ),
            $serializedConfig
        );
    }
}
