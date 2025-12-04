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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(CollectionPagerRuntime::class)]
final class CollectionPagerRuntimeTest extends TestCase
{
    private Closure $routeGenerator;

    private Stub&ViewInterface $pagerfantaViewStub;

    private CollectionPagerRuntime $runtime;

    protected function setUp(): void
    {
        $this->routeGenerator = static fn (Block $block, string $collectionIdentifier, int $page): string => '/generated/uri?page=' . $page;

        $this->pagerfantaViewStub = self::createStub(ViewInterface::class);

        $this->runtime = new CollectionPagerRuntime(
            $this->routeGenerator,
            $this->pagerfantaViewStub,
        );
    }

    public function testRenderCollectionPager(): void
    {
        $block = new Block();
        $pagerfantaStub = self::createStub(PagerfantaInterface::class);

        $this->pagerfantaViewStub
            ->method('render')
            ->with(
                self::identicalTo($pagerfantaStub),
                self::identicalTo($this->routeGenerator),
                self::identicalTo(['block' => $block, 'collection_identifier' => 'default']),
            )
            ->willReturn('rendered view');

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfantaStub,
            $block,
            'default',
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    public function testRenderCollectionPagerWithOptions(): void
    {
        $block = new Block();
        $pagerfantaStub = self::createStub(PagerfantaInterface::class);

        $this->pagerfantaViewStub
            ->method('render')
            ->with(
                self::identicalTo($pagerfantaStub),
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
            $pagerfantaStub,
            $block,
            'default',
            [
                'var' => 'value',
            ],
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    public function testGetCollectionPageUrl(): void
    {
        $pagerfantaStub = self::createStub(Pagerfanta::class);
        $pagerfantaStub
            ->method('getNbPages')
            ->willReturn(5);

        $uri = $this->runtime->getCollectionPageUrl(
            $pagerfantaStub,
            new Block(),
            'default',
            5,
        );

        self::assertSame('/generated/uri?page=5', $uri);
    }

    #[DataProvider('invalidPageDataProvider')]
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithInvalidPage(int $page): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Argument "page" has an invalid value\. Page -?\d+ is out of bounds$/');

        $pagerfantaStub = self::createStub(Pagerfanta::class);
        $pagerfantaStub
            ->method('getNbPages')
            ->willReturn(5);

        $this->runtime->getCollectionPageUrl(
            $pagerfantaStub,
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
