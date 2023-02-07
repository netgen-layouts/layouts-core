<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\BlockView;

use Doctrine\Common\Collections\ArrayCollection;
use Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\BlockView;
use Netgen\Layouts\View\ViewInterface;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function sprintf;

final class GetCollectionPagerListenerTest extends TestCase
{
    private MockObject $resultBuilderMock;

    private RequestStack $requestStack;

    private GetCollectionPagerListener $listener;

    protected function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);
        $this->requestStack = new RequestStack();

        $this->listener = new GetCollectionPagerListener(
            new PagerFactory(
                $this->resultBuilderMock,
                200,
            ),
            $this->requestStack,
            [ViewInterface::CONTEXT_AJAX],
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderView(): void
    {
        $request = Request::create('/');
        $request->query->set('page', '3');

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->willReturnMap(
                [
                    [$collection, 0, 0, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                    [$collection, 13, 5, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                ],
            );

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(PagerfantaInterface::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollection(): void
    {
        $request = Request::create('/');
        $request->query->set('page', '3');

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                        'paged_collections:max_pages' => Parameter::fromArray(['value' => 2]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->willReturnMap(
                [
                    [$collection, 0, 0, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                    [$collection, 8, 5, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                ],
            );

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(PagerfantaInterface::class, $event->getParameters()['pager']);
        self::assertSame(2, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndEmptyMaxPages(): void
    {
        $request = Request::create('/');
        $request->query->set('page', '3');

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                        'paged_collections:max_pages' => Parameter::fromArray(['value' => null]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->willReturnMap(
                [
                    [$collection, 0, 0, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                    [$collection, 13, 5, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                ],
            );

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(PagerfantaInterface::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::getMaxPages
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithPagedCollectionAndDisabledPaging(): void
    {
        $request = Request::create('/');
        $request->query->set('page', '3');

        $this->requestStack->push($request);

        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => [
                        'paged_collections:enabled' => Parameter::fromArray(['value' => false]),
                    ],
                    'collections' => new ArrayCollection(
                        [
                            'default' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->addParameter('collection_identifier', 'default');

        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->willReturnMap(
                [
                    [$collection, 0, 0, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                    [$collection, 13, 5, 0, ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection])],
                ],
            );

        $this->listener->onRenderView($event);

        $result = $event->getParameters()['collection'];

        self::assertInstanceOf(ResultSet::class, $result);
        self::assertSame($collection, $result->getCollection());
        self::assertSame(1000, $result->getTotalCount());

        self::assertInstanceOf(PagerfantaInterface::class, $event->getParameters()['pager']);
        self::assertSame(3, $event->getParameters()['pager']->getCurrentPage());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCurrentRequest(): void
    {
        $view = new BlockView(new Block());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $view->addParameter('collection_identifier', 'default');
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoBlockView(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new View(new Value());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $view->addParameter('collection_identifier', 'default');
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithNoCollectionIdentifier(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(new Block());
        $view->setContext(ViewInterface::CONTEXT_AJAX);
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionPagerListener::onRenderView
     */
    public function testOnRenderViewWithWrongContext(): void
    {
        $this->requestStack->push(Request::create('/'));

        $view = new BlockView(new Block());
        $view->addParameter('collection_identifier', 'default');
        $view->setContext(ViewInterface::CONTEXT_ADMIN);
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        self::assertSame([], $event->getParameters());
    }
}
