<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\TwigBlockDefinition;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Block\Stubs\TwigBlockDefinitionHandler;
use PHPUnit\Framework\TestCase;

class TwigBlockDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\TwigBlockDefinitionHandlerInterface
     */
    protected $handler;

    /**
     * @var \Netgen\BlockManager\Block\TwigBlockDefinitionInterface
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->handler = new TwigBlockDefinitionHandler();

        $this->blockDefinition = new TwigBlockDefinition(
            array(
                'handler' => $this->handler,
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\TwigBlockDefinition::getTwigBlockName
     */
    public function testGetBlockName()
    {
        $this->assertEquals('twig_block', $this->blockDefinition->getTwigBlockName(new Block()));
    }
}
