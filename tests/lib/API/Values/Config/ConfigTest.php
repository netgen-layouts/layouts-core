<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Config;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\Config\ConfigDefinition;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Config\Config::getConfigKey
     * @covers \Netgen\BlockManager\API\Values\Config\Config::getDefinition
     */
    public function testSetProperties(): void
    {
        $definition = new ConfigDefinition();

        $config = Config::fromArray(
            [
                'configKey' => 'config',
                'definition' => $definition,
            ]
        );

        self::assertSame('config', $config->getConfigKey());
        self::assertSame($definition, $config->getDefinition());
    }
}
