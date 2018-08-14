<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Config;

use Netgen\BlockManager\API\Values\Config\ConfigStruct;
use Netgen\BlockManager\Tests\API\Stubs\ConfigAwareStruct;
use PHPUnit\Framework\TestCase;

final class ConfigAwareStructTraitTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Tests\API\Stubs\ConfigAwareStruct
     */
    private $struct;

    public function setUp(): void
    {
        $this->struct = new ConfigAwareStruct();
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testGetConfigStruct(): void
    {
        $struct = new ConfigStruct();

        $this->struct->setConfigStruct('test', $struct);

        self::assertSame($struct, $this->struct->getConfigStruct('test'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     * @expectedException \Netgen\BlockManager\Exception\API\ConfigException
     * @expectedExceptionMessage Config struct with config key "unknown" does not exist.
     */
    public function testGetConfigStructThrowsConfigException(): void
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->struct->getConfigStruct('unknown');
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::hasConfigStruct
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testHasConfigStruct(): void
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        self::assertTrue($this->struct->hasConfigStruct('test'));
        self::assertFalse($this->struct->hasConfigStruct('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::getConfigStructs
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareStructTrait::setConfigStruct
     */
    public function testGetConfigStructs(): void
    {
        $struct = new ConfigStruct();

        $this->struct->setConfigStruct('test', $struct);

        self::assertSame(['test' => $struct], $this->struct->getConfigStructs());
    }
}
