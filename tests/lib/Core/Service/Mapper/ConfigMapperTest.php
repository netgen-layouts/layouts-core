<?php

namespace Netgen\BlockManager\Tests\Core\Service\Mapper;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\API\Values\Config\ConfigCollection;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistry;
use Netgen\BlockManager\Core\Service\Mapper\ConfigMapper;
use Netgen\BlockManager\Core\Service\Mapper\ParameterMapper;
use Netgen\BlockManager\Tests\Config\Stubs\Block\HttpCacheConfigHandler;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinition;
use PHPUnit\Framework\TestCase;

class ConfigMapperTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\Registry\ConfigDefinitionRegistryInterface
     */
    protected $configDefinitionRegistry;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionInterface
     */
    protected $configDefinition;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $parameterMapperMock;

    /**
     * @var \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper
     */
    protected $mapper;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->parameterMapperMock = $this->createMock(ParameterMapper::class);

        $this->configDefinitionRegistry = new ConfigDefinitionRegistry();
        $this->configDefinition = new ConfigDefinition('block', 'test', new HttpCacheConfigHandler());
        $this->configDefinitionRegistry->addConfigDefinition('block', 'test', $this->configDefinition);

        $this->mapper = new ConfigMapper(
            $this->parameterMapperMock,
            $this->configDefinitionRegistry
        );
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::__construct
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::mapConfig
     */
    public function testMapConfig()
    {
        $this->parameterMapperMock
            ->expects($this->once())
            ->method('mapParameters')
            ->with(
                $this->equalTo($this->configDefinition),
                $this->equalTo(array('config' => 'value'))
            )
            ->will($this->returnValue(array('config' => 'mapped_value')));

        $mappedConfig = $this->mapper->mapConfig(
            'block',
            array(
                'test' => array(
                    'config' => 'value',
                ),
            )
        );

        $this->assertInstanceOf(ConfigCollection::class, $mappedConfig);
        $this->assertTrue($mappedConfig->hasConfig('test'));
        $this->assertInstanceOf(Config::class, $mappedConfig->getConfig('test'));

        $config = $mappedConfig->getConfig('test');

        $this->assertEquals('test', $config->getIdentifier());
        $this->assertEquals($this->configDefinition, $config->getDefinition());
        $this->assertEquals(array('config' => 'mapped_value'), $config->getParameters());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Service\Mapper\ConfigMapper::serializeValues
     */
    public function testSerializeValues()
    {
        $this->parameterMapperMock
            ->expects($this->once())
            ->method('serializeValues')
            ->with(
                $this->equalTo($this->configDefinition),
                $this->equalTo(array('config' => 'value')),
                $this->equalTo(array('fallback' => 'value'))
            )
            ->will($this->returnValue(array('config' => 'serialized_value')));

        $configStruct = new ConfigStruct();
        $configStruct->setParameterValue('config', 'value');

        $serializedConfig = $this->mapper->serializeValues(
            'block',
            array(
                'test' => $configStruct,
            ),
            array(
                'test' => array(
                    'fallback' => 'value',
                ),
            )
        );

        $this->assertEquals(
            array(
                'test' => array(
                    'config' => 'serialized_value',
                ),
            ),
            $serializedConfig
        );
    }
}
