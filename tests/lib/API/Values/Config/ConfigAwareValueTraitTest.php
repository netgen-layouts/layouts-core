<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\API\Values\Config;

use Netgen\BlockManager\API\Values\Config\Config;
use Netgen\BlockManager\Exception\API\ConfigException;
use Netgen\BlockManager\Tests\API\Stubs\ConfigAwareValue;
use PHPUnit\Framework\TestCase;

final class ConfigAwareValueTraitTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait::getConfigs
     */
    public function testDefaultProperties(): void
    {
        $value = new ConfigAwareValue();

        self::assertCount(0, $value->getConfigs());
    }

    /**
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait::getConfig
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait::getConfigs
     * @covers \Netgen\BlockManager\API\Values\Config\ConfigAwareValueTrait::hasConfig
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

        self::assertCount(1, $value->getConfigs());
        self::assertSame($config, $value->getConfigs()['config']);
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
