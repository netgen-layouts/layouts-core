<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContainerDefinition::class)]
final class ContainerDefinitionTest extends TestCase
{
    private ContainerDefinition $blockDefinition;

    protected function setUp(): void
    {
        $handler = new ContainerDefinitionHandler();

        $this->blockDefinition = ContainerDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $handler,
            ],
        );
    }

    public function testGetPlaceholders(): void
    {
        self::assertSame(['left', 'right'], $this->blockDefinition->getPlaceholders());
    }

    public function testGetDynamicParameters(): void
    {
        $dynamicParameters = $this->blockDefinition->getDynamicParameters(new Block());

        self::assertCount(1, $dynamicParameters);
        self::assertArrayHasKey('definition_param', $dynamicParameters);
        self::assertSame('definition_value', $dynamicParameters['definition_param']);
    }

    public function testIsContextual(): void
    {
        self::assertFalse($this->blockDefinition->isContextual(new Block()));
    }
}
