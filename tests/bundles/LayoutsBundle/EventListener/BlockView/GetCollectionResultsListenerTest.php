<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\EventListener\BlockView;

use Netgen\Bundle\LayoutsBundle\EventListener\BlockView\GetCollectionResultsListener;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Collection\Collection;
use Netgen\Layouts\API\Values\Collection\CollectionList;
use Netgen\Layouts\API\Values\Collection\Query;
use Netgen\Layouts\Block\BlockDefinition;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultBuilderInterface;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\Parameters\Parameter;
use Netgen\Layouts\Parameters\ParameterList;
use Netgen\Layouts\Tests\API\Stubs\Value;
use Netgen\Layouts\Tests\View\Stubs\View;
use Netgen\Layouts\View\View\BlockView;
use Netgen\Layouts\View\ViewInterface;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(GetCollectionResultsListener::class)]
final class GetCollectionResultsListenerTest extends TestCase
{
    private MockObject&ResultBuilderInterface $resultBuilderMock;

    private GetCollectionResultsListener $listener;

    protected function setUp(): void
    {
        $this->resultBuilderMock = $this->createMock(ResultBuilderInterface::class);

        $this->listener = new GetCollectionResultsListener(
            new PagerFactory(
                $this->resultBuilderMock,
                200,
            ),
            [ViewInterface::CONTEXT_DEFAULT, ViewInterface::CONTEXT_APP],
        );
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnRenderView(): void
    {
        $collection1 = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);
        $collection2 = Collection::fromArray(['offset' => 5, 'limit' => 10, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => CollectionList::fromArray(
                        [
                            'collection1' => $collection1,
                            'collection2' => $collection2,
                        ],
                    ),
                ],
            ),
        );

        $view->context = ViewInterface::CONTEXT_DEFAULT;
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->willReturnMap(
                [
                    [$collection1, 3, 5, 0, ResultSet::fromArray(['collection' => $collection1, 'totalCount' => 0])],
                    [$collection2, 5, 10, 0, ResultSet::fromArray(['collection' => $collection2, 'totalCount' => 0])],
                ],
            );

        $this->listener->onRenderView($event);

        $collections = $event->parameters['collections'];

        self::assertArrayHasKey('collection1', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection1']);
        self::assertSame($collection1, $collections['collection1']->collection);
        self::assertSame(0, $collections['collection1']->totalCount);

        self::assertArrayHasKey('collection2', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection2']);
        self::assertSame($collection2, $collections['collection2']->collection);
        self::assertSame(0, $collections['collection2']->totalCount);

        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection1']);
        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection2']);
    }

    public function testOnRenderViewWithPagedCollection(): void
    {
        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => new ParameterList(
                        [
                            'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                            'paged_collections:max_pages' => Parameter::fromArray(['value' => 2]),
                        ],
                    ),
                    'collections' => CollectionList::fromArray(
                        [
                            'collection' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->context = ViewInterface::CONTEXT_DEFAULT;
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection]));

        $this->listener->onRenderView($event);

        $collections = $event->parameters['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->collection);
        self::assertSame(1000, $collections['collection']->totalCount);

        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection']);
        self::assertSame(10, $event->parameters['pagers']['collection']->getNbResults());
    }

    public function testOnRenderViewWithPagedCollectionAndEmptyMaxPages(): void
    {
        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => new ParameterList(
                        [
                            'paged_collections:enabled' => Parameter::fromArray(['value' => true]),
                            'paged_collections:max_pages' => Parameter::fromArray(['value' => null]),
                        ],
                    ),
                    'collections' => CollectionList::fromArray(
                        [
                            'collection' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->context = ViewInterface::CONTEXT_DEFAULT;
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection]));

        $this->listener->onRenderView($event);

        $collections = $event->parameters['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->collection);
        self::assertSame(1000, $collections['collection']->totalCount);

        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection']);
        self::assertSame(997, $event->parameters['pagers']['collection']->getNbResults());
    }

    public function testOnRenderViewWithPagedCollectionAndDisabledPaging(): void
    {
        $collection = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => BlockDefinition::fromArray(
                        [
                            'handlerPlugins' => [new PagedCollectionsPlugin([], [])],
                        ],
                    ),
                    'parameters' => new ParameterList(
                        [
                            'paged_collections:enabled' => Parameter::fromArray(['value' => false]),
                        ],
                    ),
                    'collections' => CollectionList::fromArray(
                        [
                            'collection' => $collection,
                        ],
                    ),
                ],
            ),
        );

        $view->context = ViewInterface::CONTEXT_DEFAULT;
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->with(
                self::identicalTo($collection),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(0),
            )
            ->willReturn(ResultSet::fromArray(['totalCount' => 1000, 'collection' => $collection]));

        $this->listener->onRenderView($event);

        $collections = $event->parameters['collections'];

        self::assertArrayHasKey('collection', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection']);
        self::assertSame($collection, $collections['collection']->collection);
        self::assertSame(1000, $collections['collection']->totalCount);

        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection']);
        self::assertSame(997, $event->parameters['pagers']['collection']->getNbResults());
    }

    public function testOnRenderViewWithAPIContext(): void
    {
        $collection1 = Collection::fromArray(['offset' => 3, 'limit' => 5, 'query' => new Query()]);

        $view = new BlockView(
            Block::fromArray(
                [
                    'definition' => new BlockDefinition(),
                    'collections' => CollectionList::fromArray(
                        [
                            'collection1' => $collection1,
                        ],
                    ),
                ],
            ),
        );

        $view->context = ViewInterface::CONTEXT_APP;
        $event = new CollectViewParametersEvent($view);

        $this->resultBuilderMock
            ->method('build')
            ->with(
                self::identicalTo($collection1),
                self::identicalTo(3),
                self::identicalTo(5),
                self::identicalTo(ResultSet::INCLUDE_UNKNOWN_ITEMS),
            )
            ->willReturn(ResultSet::fromArray(['collection' => $collection1, 'totalCount' => 0]));

        $this->listener->onRenderView($event);

        $collections = $event->parameters['collections'];

        self::assertArrayHasKey('collection1', $collections);
        self::assertInstanceOf(ResultSet::class, $collections['collection1']);
        self::assertSame($collection1, $collections['collection1']->collection);
        self::assertSame(0, $collections['collection1']->totalCount);

        self::assertInstanceOf(PagerfantaInterface::class, $event->parameters['pagers']['collection1']);
    }

    public function testOnRenderViewWithNoBlockView(): void
    {
        $view = new View(new Value());
        $event = new CollectViewParametersEvent($view);
        $this->listener->onRenderView($event);

        self::assertSame([], $event->parameters);
    }

    public function testOnRenderViewWithWrongContext(): void
    {
        $view = new BlockView(new Block());
        $view->context = ViewInterface::CONTEXT_ADMIN;
        $event = new CollectViewParametersEvent($view);

        $this->listener->onRenderView($event);

        self::assertSame([], $event->parameters);
    }
}
