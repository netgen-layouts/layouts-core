<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Core\Values\Config;

use Netgen\BlockManager\Core\Values\Config\Config;
use Netgen\BlockManager\Exception\Core\ConfigException;
use Netgen\BlockManager\Tests\Core\Stubs\ConfigAwareValue;
use PHPUnit\Framework\TestCase;

final class ConfigAwareValueTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigs
     */
    public function testDefaultProperties(): void
    {
        $value = new ConfigAwareValue();

        self::assertSame([], $value->getConfigs());
    }

    /**
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfig
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::getConfigs
     * @covers \Netgen\BlockManager\Core\Values\Config\ConfigAwareValueTrait::hasConfig
     */
    public function testSetProperties(): void
    {
        $config = new Config();

        $value = ConfigAwareValue::fromArray(
            [
                'configs' => [
                    'config' => $config,
                ],
            ]
        );

        self::assertSame(['config' => $config], $value->getConfigs());
        self::assertTrue($value->hasConfig('config'));
        self::assertFalse($value->hasConfig('unknown'));
        self::assertSame($config, $value->getConfig('config'));

        try {
            $value->getConfig('unknown');
        } catch (ConfigException $e) {
            // Do nothing
        }
    }
}
