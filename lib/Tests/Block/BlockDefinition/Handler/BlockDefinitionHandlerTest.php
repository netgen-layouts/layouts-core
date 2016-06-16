<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Parameters\Parameter;
use PHPUnit\Framework\TestCase;

class BlockDefinitionHandlerTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler
     */
    protected $handler;

    public function setUp()
    {
        $this->handler = $this->getMockForAbstractClass(BlockDefinitionHandler::class);
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'css_id' => new Parameter\TextLine(),
                'css_class' => new Parameter\TextLine(),
            ),
            $this->handler->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\BlockDefinitionHandler::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        self::assertEquals(array(), $this->handler->getDynamicParameters(new Block()));
    }
}
