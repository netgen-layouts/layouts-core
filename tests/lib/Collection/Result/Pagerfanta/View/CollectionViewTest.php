<?php

declare(strict_types=1);

namespace Netgen\Layouts\Tests\Collection\Result\Pagerfanta\View;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Collection\Result\Pagerfanta\View\CollectionView;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

#[CoversClass(CollectionView::class)]
final class CollectionViewTest extends TestCase
{
    private MockObject $twigMock;

    private CollectionView $collectionView;

    protected function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->collectionView = new CollectionView($this->twigMock, 'default_template.html.twig');
    }

    public function testGetName(): void
    {
        self::assertSame('nglayouts_collection', $this->collectionView->getName());
    }

    public function testRender(): void
    {
        $block = new Block();
        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo('default_template.html.twig'),
                self::identicalTo(
                    [
                        'block' => $block,
                        'collection_identifier' => 'default',
                        'pager' => $pagerMock,
                    ],
                ),
            )
            ->willReturn('rendered template');

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => $block,
                'collection_identifier' => 'default',
            ],
        );

        self::assertSame('rendered template', $renderedTemplate);
    }

    public function testRenderWithOverriddenTemplate(): void
    {
        $block = new Block();
        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo('template.html.twig'),
                self::identicalTo(
                    [
                        'block' => $block,
                        'collection_identifier' => 'default',
                        'pager' => $pagerMock,
                    ],
                ),
            )
            ->willReturn('rendered template');

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => $block,
                'collection_identifier' => 'default',
                'template' => 'template.html.twig',
            ],
        );

        self::assertSame('rendered template', $renderedTemplate);
    }

    public function testRenderThrowsInvalidArgumentExceptionWithNoBlock(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render the collection view, "block" option must be an instance of Netgen\Layouts\API\Values\Block\Block');

        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'collection_identifier' => 'default',
            ],
        );
    }

    public function testRenderThrowsInvalidArgumentExceptionWithInvalidBlock(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render the collection view, "block" option must be an instance of Netgen\Layouts\API\Values\Block\Block');

        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => 'block',
                'collection_identifier' => 'default',
            ],
        );
    }

    public function testRenderThrowsInvalidArgumentExceptionWithNoCollectionIdentifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render the collection view, "collection_identifier" option must be a string');

        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
            ],
        );
    }

    public function testRenderThrowsInvalidArgumentExceptionWithInvalidCollectionIdentifier(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('To render the collection view, "collection_identifier" option must be a string');

        $pagerMock = $this->createMock(PagerfantaInterface::class);

        $this->twigMock->expects(self::never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
                'collection_identifier' => 42,
            ],
        );
    }

    private function getRouteGenerator(): callable
    {
        return static fn (Block $block, string $collectionIdentifier, int $page): string => '/route/' . $page;
    }
}
