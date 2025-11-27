<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinition;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigDefinition::class)]
final class ConfigDefinitionTest extends TestCase
{
    private ConfigDefinition $configDefinition;

    protected function setUp(): void
    {
        $this->configDefinition = ConfigDefinition::fromArray(
            [
                'configKey' => 'config_definition',
            ],
        );
    }

    public function testGetConfigKey(): void
    {
        self::assertSame('config_definition', $this->configDefinition->configKey);
    }
}
