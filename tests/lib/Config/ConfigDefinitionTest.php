<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Config;

use Netgen\Layouts\Config\ConfigDefinition;
use Netgen\Layouts\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionTest extends TestCase
{
    private ConfigDefinitionHandler $handler;

    private ConfigDefinition $configDefinition;

    protected function setUp(): void
    {
        $this->handler = new ConfigDefinitionHandler();

        $this->configDefinition = ConfigDefinition::fromArray(
            [
                'configKey' => 'config_definition',
                'handler' => $this->handler,
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
