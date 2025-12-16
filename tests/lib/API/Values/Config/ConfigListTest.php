<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigList::class)]
final class ConfigListTest extends TestCase
{
    public function testGetConfigs(): void
    {
        $configs = ['key1' => new Config(), 'key2' => new Config()];

        self::assertSame($configs, new ConfigList($configs)->getConfigs());
    }

    public function testGetConfigKeys(): void
    {
        $configs = [
            'key1' => Config::fromArray(['configKey' => 'key1']),
            'key2' => Config::fromArray(['configKey' => 'key2']),
        ];

        self::assertSame(['key1', 'key2'], new ConfigList($configs)->getConfigKeys());
    }
}
