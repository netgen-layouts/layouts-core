<?php

namespace Netgen\BlockManager\Tests\View\View;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\Configuration;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinitionHandler;
use Netgen\BlockManager\View\View\BlockView;
use PHPUnit\Framework\TestCase;

class BlockViewTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\API\Values\Page\Block
     */
    protected $block;

    /**
     * @var \Netgen\BlockManager\Block\BlockDefinitionInterface
     */
    protected $blockDefinition;

    /**
     * @var \Netgen\BlockManager\View\View\BlockViewInterface
     */
    protected $view;

    public function setUp()
    {
        $this->block = new Block(array('id' => 42));
        $this->blockDefinition = new BlockDefinition(
            'block_definition',
            new BlockDefinitionHandler(),
            new Configuration('block_definition', array(), array())
        );

        $this->view = new BlockView($this->block, $this->blockDefinition);
        $this->view->addParameters(array('param' => 'value'));
        $this->view->addParameters(array('block' => 42));
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::__construct
     * @covers \Netgen\BlockManager\View\View\BlockView::getBlock
     */
    public function testGetBlock()
    {
        self::assertEquals($this->block, $this->view->getBlock());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::getBlockDefinition
     */
    public function testGetBlockDefinition()
    {
        self::assertEquals($this->blockDefinition, $this->view->getBlockDefinition());
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'param' => 'value',
                'block' => $this->block,
                'block_definition' => $this->blockDefinition,
            ),
            $this->view->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\View\View\BlockView::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('block_view', $this->view->getIdentifier());
    }
}
