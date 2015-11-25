<?php

namespace Netgen\BlockManager\Tests\Configuration;

use Netgen\BlockManager\Tests\Configuration\Stubs\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockConfig
     */
    public function testGetBlockConfig()
    {
        $configuration = new Configuration();

        self::assertEquals(
            array('name' => 'Some block'),
            $configuration->getBlockConfig('some_block')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockConfig
     * @expectedException \RuntimeException
     */
    public function testGetBlockConfigThrowsRuntimeException()
    {
        $configuration = new Configuration();
        $configuration->getBlockConfig('some_other_block');
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockTypeConfig
     */
    public function testGetBlockTypeConfig()
    {
        $configuration = new Configuration();

        self::assertEquals(
            array('type_name' => 'Some block type'),
            $configuration->getBlockTypeConfig('some_block_type')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getBlockTypeConfig
     * @expectedException \RuntimeException
     */
    public function testGetBlockTypeConfigThrowsRuntimeException()
    {
        $configuration = new Configuration();
        $configuration->getBlockTypeConfig('some_other_block_type');
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getLayoutConfig
     */
    public function testGetLayoutConfig()
    {
        $configuration = new Configuration();

        self::assertEquals(
            array('name' => 'Some layout'),
            $configuration->getLayoutConfig('some_layout')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Configuration\Configuration::getLayoutConfig
     * @expectedException \RuntimeException
     */
    public function testGetLayoutConfigThrowsRuntimeException()
    {
        $configuration = new Configuration();
        $configuration->getLayoutConfig('some_other_layout');
    }
}
