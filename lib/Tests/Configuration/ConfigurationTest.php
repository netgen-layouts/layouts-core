<?php

namespace Netgen\BlockManager\Tests\Configuration;

use Netgen\BlockManager\Tests\Configuration\Stubs\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Configuration\Configuration
     */
    protected $configuration;

    public function setUp()
    {
        $this->configuration = new Configuration();
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockDefinitionConfig
     */
    public function testGetBlockDefinitionConfig()
    {
        self::assertEquals(
            array('name' => 'Some block'),
            $this->configuration->getBlockDefinitionConfig('some_block')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockDefinitionConfig
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockDefinitionConfigThrowsInvalidArgumentException()
    {
        $this->configuration->getBlockDefinitionConfig('some_other_block');
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockTypeConfig
     */
    public function testGetBlockTypeConfig()
    {
        self::assertEquals(
            array('name' => 'Some block type'),
            $this->configuration->getBlockTypeConfig('some_block_type')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockTypeConfig
     * @expectedException \InvalidArgumentException
     */
    public function testGetBlockTypeConfigThrowsInvalidArgumentException()
    {
        $this->configuration->getBlockTypeConfig('some_other_block_type');
    }
}
