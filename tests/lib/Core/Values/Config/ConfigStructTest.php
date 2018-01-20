<?php

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait;
use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use PHPUnit\Framework\TestCase;

final class ConfigStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait
     */
    private $struct;

    public function setUp()
    {
        $this->struct = $this->getMockForTrait(ConfigAwareStructTrait::class);
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     */
    public function testGetConfigStruct()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertEquals(new ConfigStruct(), $this->struct->getConfigStruct('test'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     * @expectedException \Netgen\BlockManager\Exception\Core\ConfigException
     * @expectedExceptionMessage Config struct with config key "unknown" does not exist.
     */
    public function testGetConfigStructThrowsConfigException()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->struct->getConfigStruct('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::hasConfigStruct
     */
    public function testHasConfigStruct()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertTrue($this->struct->hasConfigStruct('test'));
        $this->assertFalse($this->struct->hasConfigStruct('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStructs
     */
    public function testGetConfigStructs()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertEquals(array('test' => new ConfigStruct()), $this->struct->getConfigStructs());
    }
}
