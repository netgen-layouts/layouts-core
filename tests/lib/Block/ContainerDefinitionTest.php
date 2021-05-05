<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ContainerDefinitionTest extends TestCase
{
    private ContainerDefinitionHandler $handler;

    private ContainerDefinition $blockDefinition;

    protected function setUp(): void
    {
        $this->handler = new ContainerDefinitionHandler();

        $this->blockDefinition = ContainerDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ],
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders(): void
    {
        self::assertSame(['left', 'right'], $this->blockDefinition->getPlaceholders());
    }

    /**
     * @covers \Netgen\Layouts\Block\ContainerDefinition::getDynamicParameters
     * @covers \Netgen\Layouts\Block\ContainerDefinition::getHandler
     */
    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(1, $dynamicParameters);
        self::assertArrayHasKey('definition_param', $dynamicParameters);
        self::assertSame('definition_value', $dynamicParameters['definition_param']);
    }

    /**
     * @covers \Netgen\Layouts\Block\ContainerDefinition::getHandler
     * @covers \Netgen\Layouts\Block\ContainerDefinition::isContextual
     */
    public function testIsContextual(): void
    {
        self::assertFalse($this->blockDefinition->isContextual(new Block()));
    }
}
