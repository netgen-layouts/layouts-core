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
            self::createStub(Template::class),
            ['param' => 'value'],
        );

        self::assertSame(['param' => 'value'], $template->getContext());
    }

    public function testHasBlock(): void
    {
        $templateStub = self::createStub(Template::class);

        $templateStub
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $template = new ContextualizedTwigTemplate($templateStub);

        self::assertTrue($template->hasBlock('block_name'));
    }

    public function testHasBlockReturnsFalse(): void
    {
        $templateStub = self::createStub(Template::class);

        $templateStub
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(false);

        $template = new ContextualizedTwigTemplate($templateStub);

        self::assertFalse($template->hasBlock('block_name'));
    }

    public function testRenderBlock(): void
    {
        $templateStub = self::createStub(Template::class);

        $templateStub
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $templateStub
            ->method('displayBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturnCallback(
                static function (string $blockName): void {
                    echo 'rendered';
                },
            );

        $template = new ContextualizedTwigTemplate($templateStub);

        self::assertSame('rendered', $template->renderBlock('block_name'));
    }

    public function testRenderBlockNonExistingBlock(): void
    {
        $templateStub = self::createStub(Template::class);

        $templateStub
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(false);

        $template = new ContextualizedTwigTemplate($templateStub);

        self::assertSame('', $template->renderBlock('block_name'));
    }

    public function testRenderBlockWithException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception text');

        $templateStub = self::createStub(Template::class);

        $templateStub
            ->method('hasBlock')
            ->with(self::identicalTo('block_name'))
            ->willReturn(true);

        $templateStub
            ->method('displayBlock')
            ->with(self::identicalTo('block_name'))
            ->willThrowException(new Exception('Test exception text'));

        $template = new ContextualizedTwigTemplate($templateStub);
        $template->renderBlock('block_name');
    }
}
