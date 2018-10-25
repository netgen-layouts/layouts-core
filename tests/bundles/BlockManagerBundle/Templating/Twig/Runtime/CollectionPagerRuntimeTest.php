<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Exception\InvalidArgumentException;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewInterface;
use PHPUnit\Framework\TestCase;

final class CollectionPagerRuntimeTest extends TestCase
{
    /**
     * @var callable
     */
    private $routeGenerator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $pagerfantaViewMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime
     */
    private $runtime;

    public function setUp(): void
    {
        $this->routeGenerator = function (Block $block, string $collectionIdentifier, int $page): string {
            return '/generated/uri' . '?page=' . $page;
        };

        $this->pagerfantaViewMock = $this->createMock(ViewInterface::class);

        $this->runtime = new CollectionPagerRuntime(
            $this->routeGenerator,
            $this->pagerfantaViewMock
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPager(): void
    {
        $block = new Block();
        $pagerfanta = $this->createMock(Pagerfanta::class);

        $this->pagerfantaViewMock->expects(self::once())
            ->method('render')
            ->with(
                self::identicalTo($pagerfanta),
                self::identicalTo($this->routeGenerator),
                self::identicalTo(['block' => $block, 'collection_identifier' => 'default'])
            )
            ->will(self::returnValue('rendered view'));

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            $block,
            'default'
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPagerWithOptions(): void
    {
        $block = new Block();
        $pagerfanta = $this->createMock(Pagerfanta::class);

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
                    ]
                )
            )
            ->will(self::returnValue('rendered view'));

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            $block,
            'default',
            [
                'var' => 'value',
            ]
        );

        self::assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     */
    public function testGetCollectionPageUrl(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta->expects(self::any())
            ->method('getNbPages')
            ->will(self::returnValue(5));

        $uri = $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            5
        );

        self::assertSame('/generated/uri?page=5', $uri);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     * @dataProvider invalidPageProvider
     */
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithInvalidPage(int $page): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageRegExp('/^Argument "page" has an invalid value\\. Page -?\\d+ is out of bounds$/');

        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta->expects(self::any())
            ->method('getNbPages')
            ->will(self::returnValue(5));

        $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            $page
        );
    }

    public function invalidPageProvider(): array
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
