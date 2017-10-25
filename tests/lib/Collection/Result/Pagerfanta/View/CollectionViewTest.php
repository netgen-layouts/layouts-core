<?php

namespace Netgen\BlockManager\Tests\Collection\Result\Pagerfanta\View;

use Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class CollectionViewTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $twigMock;

    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView
     */
    private $collectionView;

    public function setUp()
    {
        $this->twigMock = $this->createMock(Environment::class);

        $this->collectionView = new CollectionView($this->twigMock, 'default_template.html.twig');
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::__construct
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::getName
     */
    public function testGetName()
    {
        $this->assertEquals('ngbm_collection', $this->collectionView->getName());
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     */
    public function testRender()
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('default_template.html.twig'),
                $this->equalTo(
                    array(
                        'pager' => $pagerMock,
                        'pager_uri' => '/route/1',
                        'var' => 'value',
                    )
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            array(
                'var' => 'value',
            )
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    /**
     * @covers \Netgen\BlockManager\Collection\Result\Pagerfanta\View\CollectionView::render
     */
    public function testRenderWithOverridenTemplate()
    {
        $pagerMock = $this->createMock(Pagerfanta::class);

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with(
                $this->equalTo('template.html.twig'),
                $this->equalTo(
                    array(
                        'pager' => $pagerMock,
                        'pager_uri' => '/route/1',
                        'var' => 'value',
                    )
                )
            )
            ->will($this->returnValue('rendered template'));

        $renderedTemplate = $this->collectionView->render(
            $pagerMock,
            $this->getRouteGenerator(),
            array(
                'var' => 'value',
                'template' => 'template.html.twig',
            )
        );

        $this->assertEquals('rendered template', $renderedTemplate);
    }

    private function getRouteGenerator()
    {
        return function ($page) {
            return '/route/' . $page;
        };
    }
}
