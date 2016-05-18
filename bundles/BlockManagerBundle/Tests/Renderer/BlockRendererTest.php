<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Renderer;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;
use Exception;

class BlockRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockDefinitionRegistryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewRendererMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fragmentHandlerMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer
     */
    protected $blockRenderer;

    public function setUp()
    {
        $this->blockDefinitionRegistryMock = $this->getMock(BlockDefinitionRegistryInterface::class);

        $this->blockDefinitionRegistryMock
            ->expects($this->any())
            ->method('getBlockDefinition')
            ->with($this->equalTo('block_definition'))
            ->will($this->returnValue(new BlockDefinition()));

        $this->viewRendererMock = $this->getMock(RendererInterface::class);

        $this->fragmentHandlerMock = $this->getMock(FragmentHandler::class);

        $this->blockRenderer = new BlockRenderer(
            $this->blockDefinitionRegistryMock,
            $this->viewRendererMock,
            $this->fragmentHandlerMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer::renderBlock
     */
    public function testRenderBlock()
    {
        $block = new Block(array('definitionIdentifier' => 'block_definition'));
        $context = ViewInterface::CONTEXT_VIEW;

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->with(
                $this->equalTo($block),
                $this->equalTo($context),
                array('param' => 'value', 'definition_param' => 'definition_value')
            )
            ->will($this->returnValue('rendered block'));

        $renderedBlock = $this->blockRenderer->renderBlock(
            $block,
            $context,
            array('param' => 'value')
        );

        self::assertEquals('rendered block', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer::renderBlock
     */
    public function testRenderBlockException()
    {
        $block = new Block(array('definitionIdentifier' => 'block_definition'));
        $context = ViewInterface::CONTEXT_VIEW;

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception()));

        $renderedBlock = $this->blockRenderer->renderBlock(
            $block,
            $context,
            array('param' => 'value')
        );

        self::assertEquals('', $renderedBlock);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer::renderBlock
     * @covers \Netgen\Bundle\BlockManagerBundle\Renderer\BlockRenderer::setDebug
     * @expectedException \Exception
     */
    public function testRenderBlockExceptionInDebugMode()
    {
        $this->blockRenderer->setDebug(true);

        $block = new Block(array('definitionIdentifier' => 'block_definition'));
        $context = ViewInterface::CONTEXT_VIEW;

        $this->viewRendererMock
            ->expects($this->once())
            ->method('renderValue')
            ->will($this->throwException(new Exception()));

        $this->blockRenderer->renderBlock($block, $context, array('param' => 'value'));
    }
}
