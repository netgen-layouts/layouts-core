<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\View\Twig;

use Exception;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use PHPUnit\Framework\TestCase;
use Twig\Template;

final class ContextualizedTwigTemplateTest extends TestCase
{
    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::__construct
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     */
    public function testRenderBlock(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->identicalTo('block_name'))
            ->will($this->returnValue(true));

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->identicalTo('block_name'))
            ->will(
                $this->returnCallback(
                    function (string $blockName): void {
                        echo 'rendered';
                    }
                )
            );

        $template = new ContextualizedTwigTemplate($templateMock);

        $this->assertSame('rendered', $template->renderBlock('block_name'));
    }

    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::__construct
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     */
    public function testRenderBlockNonExistingBlock(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->identicalTo('block_name'))
            ->will($this->returnValue(false));

        $templateMock
            ->expects($this->never())
            ->method('displayBlock');

        $template = new ContextualizedTwigTemplate($templateMock);

        $this->assertSame('', $template->renderBlock('block_name'));
    }

    /**
     * @covers \Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate::renderBlock
     * @expectedException \Exception
     * @expectedExceptionMessage Test exception text
     */
    public function testRenderBlockWithException(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->expects($this->any())
            ->method('hasBlock')
            ->with($this->identicalTo('block_name'))
            ->will($this->returnValue(true));

        $templateMock
            ->expects($this->any())
            ->method('displayBlock')
            ->with($this->identicalTo('block_name'))
            ->will($this->throwException(new Exception('Test exception text')));

        $template = new ContextualizedTwigTemplate($templateMock);
        $template->renderBlock('block_name');
    }
}
