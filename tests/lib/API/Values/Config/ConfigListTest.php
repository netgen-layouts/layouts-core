<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigList;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

use function sprintf;
use function str_replace;

final class ConfigListTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::__construct
     */
    public function testConstructorWithInvalidType(): void
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessageMatches(
            sprintf(
                '/(must be an instance of|must be of type) %s, (instance of )?%s given/',
                str_replace('\\', '\\\\', Config::class),
                stdClass::class,
            ),
        );

        new ConfigList(['key1' => new Config(), 'key2' => new stdClass(), 'key3' => new Config()]);
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::__construct
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::getConfigs
     */
    public function testGetConfigs(): void
    {
        $configs = ['key1' => new Config(), 'key2' => new Config()];

        self::assertSame($configs, (new ConfigList($configs))->getConfigs());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigList::getConfigKeys
     */
    public function testGetConfigKeys(): void
    {
        $configs = [
            'key1' => Config::fromArray(['configKey' => 'key1']),
            'key2' => Config::fromArray(['configKey' => 'key2']),
        ];

        self::assertSame(['key1', 'key2'], (new ConfigList($configs))->getConfigKeys());
    }
}
