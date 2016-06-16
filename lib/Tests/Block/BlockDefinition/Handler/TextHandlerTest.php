<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TextHandler;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;

class TextHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\TextHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new TextHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TextHandler::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'content' => new Parameter\Text(array(), true),
                'css_id' => new Parameter\TextLine(),
                'css_class' => new Parameter\TextLine(),
            ),
            $this->handler->getParameters()
        );
    }
}
