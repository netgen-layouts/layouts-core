<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;

class TitleHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler
     */
    protected $handler;

    /**
     * @var array
     */
    protected $options = array(
        'Heading 1' => 'h1',
        'Heading 2' => 'h2',
        'Heading 3' => 'h3',
    );

    public function setUp()
    {
        $this->handler = new TitleHandler(array_flip($this->options));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Handler\TitleHandler::getParameters
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getCommonParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'tag' => new Parameter\Choice(
                    array('options' => $this->options),
                    true
                ),
                'title' => new Parameter\TextLine(array(), true),
                'css_id' => new Parameter\TextLine(),
                'css_class' => new Parameter\TextLine(),
            ),
            $this->handler->getParameters()
        );
    }
}
