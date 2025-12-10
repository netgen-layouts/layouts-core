<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\ConfigAwareStructTrait;
use Netgen\Layouts\API\Values\Config\ConfigStruct;
use Netgen\Layouts\Exception\API\ConfigException;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareStruct;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ConfigAwareStructTrait::class)]
final class ConfigAwareStructTraitTest extends TestCase
{
    private ConfigAwareStruct $struct;

    protected function setUp(): void
    {
        $this->struct = new ConfigAwareStruct();
    }

    public function testGetConfigStruct(): void
    {
        $struct = new ConfigStruct();

        $this->struct->setConfigStruct('test', $struct);

        self::assertSame($struct, $this->struct->getConfigStruct('test'));
    }

    public function testGetConfigStructThrowsConfigException(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Config struct with config key "unknown" does not exist.');

        $this->struct->setConfigStruct('test', new ConfigStruct());

        $this->struct->getConfigStruct('unknown');
    }

    public function testHasConfigStruct(): void
    {
        $this->struct->setConfigStruct('test', new ConfigStruct());

        self::assertTrue($this->struct->hasConfigStruct('test'));
        self::assertFalse($this->struct->hasConfigStruct('unknown'));
    }

    public function testGetConfigStructs(): void
    {
        $struct = new ConfigStruct();

        $this->struct->setConfigStruct('test', $struct);

        self::assertSame(['test' => $struct], $this->struct->configStructs);
    }
}
