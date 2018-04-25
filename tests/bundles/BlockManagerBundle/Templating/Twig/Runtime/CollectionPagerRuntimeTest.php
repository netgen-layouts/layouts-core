<?php

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

    public function setUp()
    {
        $this->routeGenerator = function (APIBlock $block, $collectionIdentifier, $page) {
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
    public function testRenderCollectionPager()
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

        $this->assertEquals('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::renderCollectionPager
     */
    public function testRenderCollectionPagerWithOptions()
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

        $this->assertEquals('rendered view', $renderedPagerfanta);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     */
    public function testGetCollectionPageUrl()
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

        $this->assertEquals('/generated/uri', $uri);
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\Runtime\CollectionPagerRuntime::getCollectionPageUrl
     * @expectedException \Netgen\BlockManager\Exception\InvalidArgumentException
     * @expectedExceptionMessage Argument "page" has an invalid value. Page -5 is out of bounds
     */
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithTooLowPage()
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
    public function testGetCollectionPageUrlThrowsInvalidArgumentExceptionWithTooLargePage()
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
