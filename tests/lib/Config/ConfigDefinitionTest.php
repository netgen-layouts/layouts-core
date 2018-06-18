<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Config;

use Netgen\BlockManager\Config\ConfigDefinition;
use Netgen\BlockManager\Tests\Config\Stubs\ConfigDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ConfigDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Config\ConfigDefinition
     */
    private $configDefinition;

    public function setUp(): void
    {
        $this->handler = new ConfigDefinitionHandler();

        $this->configDefinition = new ConfigDefinition(
            [
                'configKey' => 'config_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Config\ConfigDefinition::getConfigKey
     */
    public function testGetConfigKey(): void
    {
        $this->assertSame('config_definition', $this->configDefinition->getConfigKey());
    }
}
