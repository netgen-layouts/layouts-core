<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

final class ConfigListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage(
            sprintf(
                'Argument 1 passed to %s::%s\{closure}() must be an instance of %s, instance of %s given',
                ConfigList::class,
                str_replace('\ConfigList', '', ConfigList::class),
                Config::class,
                stdClass::class
            )
        );

        new ConfigList([new Config(), new stdClass(), new Config()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::__construct
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::getConfigs
     */
    public function testGetConfigs(): void
    {
        $configs = [new Config(), new Config()];

        self::assertSame($configs, (new ConfigList($configs))->getConfigs());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::getConfigKeys
     */
    public function testGetConfigKeys(): void
    {
        $configs = [Config::fromArray(['configKey' => 'foo']), Config::fromArray(['configKey' => 'bar'])];

        self::assertSame(['foo', 'bar'], (new ConfigList($configs))->getConfigKeys());
    }
}
