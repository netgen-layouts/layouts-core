<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler;
use Netgen\BlockManager\Parameters\Parameter;

class TwigBlockHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = new TwigBlockHandler();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TwigBlockHandler::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'block_name' => new Parameter\Identifier(array(), true),
            ),
            $this->handler->getParameters()
        );
    }
}
