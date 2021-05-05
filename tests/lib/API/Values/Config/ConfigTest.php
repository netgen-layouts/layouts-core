<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Config\ConfigDefinition;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Config\Config::getConfigKey
     * @covers \Netgen\Layouts\API\Values\Config\Config::getDefinition
     */
    public function testSetProperties(): void
    {
        $definition = new ConfigDefinition();

        $config = Config::fromArray(
            [
                'configKey' => 'config',
                'definition' => $definition,
            ],
        );

        self::assertSame('config', $config->getConfigKey());
        self::assertSame($definition, $config->getDefinition());
    }
}
