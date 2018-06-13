<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareStruct;
use PHPUnit\Framework\TestCase;

final class ConfigStructTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Config\ConfigAwareStruct
     */
    private $struct;

    public function setUp()
    {
        $this->struct = new ConfigAwareStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testGetConfigStruct()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertEquals(new ConfigStruct(), $this->struct->getConfigStruct('test'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @expectedException \Netgen\BlockManager\Exception\Core\ConfigException
     * @expectedExceptionMessage Config struct with config key "unknown" does not exist.
     */
    public function testGetConfigStructThrowsConfigException()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->struct->getConfigStruct('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::hasConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testHasConfigStruct()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertTrue($this->struct->hasConfigStruct('test'));
        $this->assertFalse($this->struct->hasConfigStruct('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStructs
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testGetConfigStructs()
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->assertEquals(['test' => new ConfigStruct()], $this->struct->getConfigStructs());
    }
}
