<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\ParagraphHandler;
use Netgen\BlockManager\Parameters\Parameter;

class ParagraphHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\ParagraphHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new ParagraphHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\ParagraphHandler::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'content' => new Parameter\TextArea(array(), true),
                'css_id' => new Parameter\Text(),
                'css_class' => new Parameter\Text(),
            ),
            $this->handler->getParameters()
        );
    }
}
