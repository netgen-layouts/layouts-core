<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\API\Values\Config;

use Netgen\Layouts\API\Values\Config\Config;
use Netgen\Layouts\Exception\API\ConfigException;
use Netgen\Layouts\Tests\API\Stubs\ConfigAwareValue;
use PHPUnit\Framework\TestCase;

final class ConfigAwareValueTraitTest extends TestCase
{
    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait::getConfigs
     */
    public function testDefaultProperties(): void
    {
        $value = new ConfigAwareValue();

        self::assertCount(0, $value->getConfigs());
    }

    /**
     * @covers \Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait::getConfig
     * @covers \Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait::getConfigs
     * @covers \Netgen\Layouts\API\Values\Config\ConfigAwareValueTrait::hasConfig
     */
    public function testSetProperties(): void
    {
        $config = new Config();

        $value = ConfigAwareValue::fromArray(
            [
                'configs' => [
                    'config' => $config,
                ],
            ],
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
