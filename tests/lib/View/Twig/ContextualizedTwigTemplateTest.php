<?php

namespace Netgen\BlockManager\Tests\View\Twig;

use Exception;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use PHPUnit\Framework\TestCase;
use Twig_Template;

class ContextualizedTwigTemplateTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::__construct
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     */
    public function testRenderBlock()
    {
        $templateMock = $this->createMock(Twig_Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->returnValue(true));

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->returnCallback(
                function ($blockName) {
                    echo 'rendered';
                }
            )
        );

        $template = new ContextualizedTwigTemplate($templateMock);

        $this->assertEquals('rendered', $template->renderBlock('block_name'));
    }

    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::__construct
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     */
    public function testRenderBlockNonExistingBlock()
    {
        $templateMock = $this->createMock(Twig_Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->returnValue(false));

        $templateMock
            ->expects($this->never())
            ->method('displayBlock');

        $template = new ContextualizedTwigTemplate($templateMock);

        $this->assertEquals('', $template->renderBlock('block_name'));
    }

    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderBlockWithException()
    {
        $templateMock = $this->createMock(Twig_Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->returnValue(true));

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->equalTo('block_name'))
            ->will($this->throwException(new Exception('Test exception text')));

        $template = new ContextualizedTwigTemplate($templateMock);
        $template->renderBlock('block_name');
    }
}
