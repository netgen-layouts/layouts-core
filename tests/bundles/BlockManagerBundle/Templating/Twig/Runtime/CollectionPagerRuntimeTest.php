<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig\Runtime;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Core\Values\Block\Block;
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
        $this->routeGenerator = function (APIBlock $block, string $collectionIdentifier, int $page): string {
            return '/generated/uri';
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
        $pagerfanta = $this->createMock(Pagerfanta::class);

        $this->pagerfantaViewMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($pagerfanta),
                $this->equalTo($this->routeGenerator),
                [
                    'block' => new Block(),
                    'collection_identifier' => 'default',
                ]
            )
            ->will($this->returnValue('rendered view'));

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            new Block(),
            'default'
        );

        $this->assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPagerWithOptions(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);

        $this->pagerfantaViewMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo($pagerfanta),
                $this->equalTo($this->routeGenerator),
                [
                    'block' => new Block(),
                    'collection_identifier' => 'default',
                    'var' => 'value',
                ]
            )
            ->will($this->returnValue('rendered view'));

        $renderedPagerfanta = $this->runtime->renderCollectionPager(
            $pagerfanta,
            new Block(),
            'default',
            [
                'var' => 'value',
            ]
        );

        $this->assertSame('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     */
    public function testGetCollectionPageUrl(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta->expects($this->any())
            ->method('getNbPages')
            ->will($this->returnValue(5));

        $uri = $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default'
        );

        $this->assertSame('/generated/uri', $uri);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Argument "page" has an invalid value. Page -5 is out of bounds
     */
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithTooLowPage(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta->expects($this->any())
            ->method('getNbPages')
            ->will($this->returnValue(5));

        $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            -5
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Argument "page" has an invalid value. Page 10 is out of bounds
     */
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithTooLargePage(): void
    {
        $pagerfanta = $this->createMock(Pagerfanta::class);
        $pagerfanta->expects($this->any())
            ->method('getNbPages')
            ->will($this->returnValue(5));

        $this->runtime->getCollectionPageUrl(
            $pagerfanta,
            new Block(),
            'default',
            10
        );
    }
}
