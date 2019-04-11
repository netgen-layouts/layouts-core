<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Block;

use Netgen\Layouts\Block\ContainerDefinition;
use Netgen\Layouts\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\Layouts\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\Layouts\Block\ContainerDefinition
     */
    private $blockDefinition;

    public function setUp(): void
    {
        $this->handler = new ContainerDefinitionHandler();

        $this->blockDefinition = ContainerDefinition::fromArray(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\Layouts\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders(): void
    {
        self::assertSame(['left', 'right'], $this->blockDefinition->getPlaceholders());
    }
}
