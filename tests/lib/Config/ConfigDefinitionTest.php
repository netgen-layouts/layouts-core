<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinition;
use PHPUnit\Framework\TestCase;

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

    /**
     * @covers \Netgen\Layouts\Config\ConfigDefinition::getConfigKey
     */
    public function testGetConfigKey(): void
    {
        self::assertSame('config_definition', $this->configDefinition->getConfigKey());
    }
}
