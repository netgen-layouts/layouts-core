<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta\View;

use Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView;
use Netgen\BlockManager\Core\Values\Block\Block;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class CollectionViewTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView
     */
    private $collectionView;

    public function setUp(): void
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->collectionView = new CollectionView($this->twigMock, 'default_template.html.twig');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::getName
     */
    public function testGetName(): void
    {
        $this->assertEquals('ngbm_collection', $this->collectionView->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     */
    public function testRender(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('default_template.html.twig'),
                $this->equalTo(
                    [
                        'pager' => $pagerMock,
                        'block' => new Block(),
                        'collection_identifier' => 'default',
                    ]
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
                'collection_identifier' => 'default',
            ]
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     */
    public function testRenderWithOverridenTemplate(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('template.html.twig'),
                $this->equalTo(
                    [
                        'pager' => $pagerMock,
                        'block' => new Block(),
                        'collection_identifier' => 'default',
                    ]
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
                'collection_identifier' => 'default',
                'template' => 'template.html.twig',
            ]
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage To render the collection view, "block" option must be an instance of Netgen\BlockManager\API\Values\Block\Block
     */
    public function testRenderThrowsInvalidArgumentExceptionWithNoBlock(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'collection_identifier' => 'default',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage To render the collection view, "block" option must be an instance of Netgen\BlockManager\API\Values\Block\Block
     */
    public function testRenderThrowsInvalidArgumentExceptionWithInvalidBlock(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => 'block',
                'collection_identifier' => 'default',
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage To render the collection view, "collection_identifier" option must be a string
     */
    public function testRenderThrowsInvalidArgumentExceptionWithNoCollectionIdentifier(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
            ]
        );
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage To render the collection view, "collection_identifier" option must be a string
     */
    public function testRenderThrowsInvalidArgumentExceptionWithInvalidCollectionIdentifier(): void
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->never())
            ->method('render');

        $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            [
                'block' => new Block(),
                'collection_identifier' => 42,
            ]
        );
    }

    private function getRouteGenerator(): callable
    {
        return function (Block $block, string $collectionIdentifier, int $page): string {
            return '/route/' . $page;
        };
    }
}
