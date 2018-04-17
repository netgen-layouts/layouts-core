<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\BlockManager\Core\Values\Block\Block;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

final class TwigBlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    private $handler;

    /**
     * @var \Netgen\BlockManager\Block\TwigBlockDefinition
     */
    private $blockDefinition;

    public function setUp()
    {
        $this->handler = new TwigBlockDefinitionHandler();

        $this->blockDefinition = new TwigBlockDefinition(
            [
                'identifier' => 'block_definition',
                'handler' => $this->handler,
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\TwigBlockDefinition::getTwigBlockName
     */
    public function testGetTwigBlockName()
    {
        $this->assertEquals('twig_block', $this->blockDefinition->getTwigBlockName(new Block()));
    }
}
