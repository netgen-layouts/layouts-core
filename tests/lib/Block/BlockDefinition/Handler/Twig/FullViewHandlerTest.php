<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Handler\Twig;

use Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\FullViewHandler;
use Netgen\BlockManager\Core\Values\Block\Block;
use PHPUnit\Framework\TestCase;

class FullViewHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\FullViewHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new FullViewHandler('content');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\TwigBlockHandler::isContextual
     */
    public function testIsContextual()
    {
        $this->assertTrue($this->handler->isContextual(new Block()));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\FullViewHandler::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\Twig\FullViewHandler::getTwigBlockName
     */
    public function testGetTwigBlockName()
    {
        $this->assertEquals('content', $this->handler->getTwigBlockName(new Block()));
    }
}
