<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\View\Twig;

use Exception;
use Netgen\Layouts\View\Twig\ContextualizedTwigTemplate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Twig\Template;

#[CoversClass(ContextualizedTwigTemplate::class)]
final class ContextualizedTwigTemplateTest extends TestCase
{
    public function testGetContext(): void
    {
        $template = new ContextualizedTwigTemplate(
            $this->createMock(Template::class),
            ['param' => 'value'],
        );

        self::assertSame(['param' => 'value'], $template->getContext());
    }

    public function testHasBlock(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $template = new ContextualizedTwigTemplate($templateMock);

        self::assertTrue($template->hasBlock('block_name'));
    }

    public function testHasBlockReturnsFalse(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(false);

        $template = new ContextualizedTwigTemplate($templateMock);

        self::assertFalse($template->hasBlock('block_name'));
    }

    public function testRenderBlock(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $templateMock
            ->method('displayBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturnCallback(
                static function (string $blockName): void {
                    echo 'rendered';
                },
            );

        $template = new ContextualizedTwigTemplate($templateMock);

        self::assertSame('rendered', $template->renderBlock('block_name'));
    }

    public function testRenderBlockNonExistingBlock(): void
    {
        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(false);

        $templateMock
            ->expects(self::never())
            ->method('displayBlock');

        $template = new ContextualizedTwigTemplate($templateMock);

        self::assertSame('', $template->renderBlock('block_name'));
    }

    public function testRenderBlockWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $templateMock = $this->createMock(Template::class);

        $templateMock
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $templateMock
            ->method('displayBlock')
            ->with(self::identicalTo('block_name'))
            ->willThrowException(new Exception('Test exception text'));

        $template = new ContextualizedTwigTemplate($templateMock);
        $template->renderBlock('block_name');
    }
}
