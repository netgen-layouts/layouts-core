<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\ContainerDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\ContainerDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class ContainerDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\ContainerDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Block\ContainerDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->handler = new ContainerDefinitionHandler();

        $this->blockDefinition = new ContainerDefinition(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\ContainerDefinition::getPlaceholders
     */
    public function testGetPlaceholders()
    {
        $this->assertEquals(['left', 'right'], $this->blockDefinition->getPlaceholders());
    }
}
