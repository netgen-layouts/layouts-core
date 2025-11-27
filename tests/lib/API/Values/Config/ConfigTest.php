<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Config\ConfigDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    public function testSetProperties(): void
    {
        $definition = new ConfigDefinition();

        $config = Config::fromArray(
            [
                'configKey' => 'config',
                'definition' => $definition,
            ],
        );

        self::assertSame('config', $config->configKey);
        self::assertSame($definition, $config->definition);
    }
}
