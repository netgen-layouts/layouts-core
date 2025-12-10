<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait;
use Netgen\Layouts\API\Values\Config\ConfigList;
use Netgen\Layouts\Exception\API\ConfigException;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareValue;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;

#[CoversTrait(ConfigAwareValueTrait::class)]
final class ConfigAwareValueTraitTest extends TestCase
{
    public function testDefaultProperties(): void
    {
        $value = ConfigAwareValue::fromArray(['configs' => new ConfigList()]);

        self::assertCount(0, $value->configs);
    }

    public function testSetProperties(): void
    {
        $config = new Config();

        $value = ConfigAwareValue::fromArray(
            [
                'configs' => new ConfigList(
                    [
                        'config' => $config,
                    ],
                ),
            ],
        );

        self::assertCount(1, $value->configs);
        self::assertSame($config, $value->configs['config']);
        self::assertTrue($value->hasConfig('config'));
        self::assertFalse($value->hasConfig('unknown'));
        self::assertSame($config, $value->getConfig('config'));

        try {
            $value->getConfig('unknown');
        } catch (ConfigException) {
            // Do nothing
        }
    }
}
