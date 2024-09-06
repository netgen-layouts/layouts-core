<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig\Runtime;

use Closure;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Pagerfanta\PagerfantaInterface;
use Pagerfanta\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CollectionPagerRuntimeTest extends TestCase
{
    private Closure $routeGenerator;

    private MockObject $pagerfantaViewMock;

    private CollectionPagerRuntime $runtime;

    protected function setUp(): void
    {
        $this->routeGenerator = static fn (Block $block, string $collectionIdentifier, int $page): string => '/generated/uri?page=' . $page;

        $this->pagerfantaViewMock = $this->createMock(ViewInterface::class);

        $this->runtime = new CollectionPagerRuntime(
            $this->routeGenerator,
            $this->pagerfantaViewMock,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPager(): void
    {
        $block = new Block();
        $pagerfanta = $this->createMock(PagerfantaInterface::class);

        $this->pagerfantaViewMock->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($pagerfanta),
                self::identicalTo($this->routeGenerator),
                self::identicalTo(['block' => $block, 'collection_identifier' => 'default']),
            )
            ->willReturn('rendered view');

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            $block,
            'default',
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPagerWithOptions(): void
    {
        $block = new Block();
        $pagerfanta = $this->createMock(PagerfantaInterface::class);

        $this->pagerfantaViewMock->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($pagerfanta),
                self::identicalTo($this->routeGenerator),
                self::identicalTo(
                    [
                        'var' => 'value',
                        'block' => $block,
                        'collection_identifier' => 'default',
                    ],
                ),
            )
            ->willReturn('rendered view');

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            $block,
            'default',
            [
                'var' => 'value',
            ],
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     */
    public function testGetCollectionPageUrl(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta
            ->method('getNbPages')
            ->willReturn(5);

        $uri = $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            5,
        );

        self::assertSame('/generated/uri?page=5', $uri);
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     *
     * @dataProvider invalidPageDataProvider
     */
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithInvalidPage(int $page): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Argument "page" has an invalid value\. Page -?\d+ is out of bounds$/');

        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta
            ->method('getNbPages')
            ->willReturn(5);

        $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            $page,
        );
    }

    public static function invalidPageDataProvider(): iterable
    {
        return [
            [-5],
            [-1],
            [0],
            [6],
            [10],
        ];
    }
}
