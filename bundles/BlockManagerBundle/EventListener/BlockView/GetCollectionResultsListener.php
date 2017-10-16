<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class GetCollectionResultsListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var array
     */
    private $enabledContexts;

    /**
     * @var int
     */
    private $maxLimit;

    public function __construct(ResultBuilderInterface $resultBuilder, array $enabledContexts, $maxLimit)
    {
        $this->resultBuilder = $resultBuilder;
        $this->enabledContexts = $enabledContexts;
        $this->maxLimit = $maxLimit;
    }

    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::RENDER_VIEW => 'onRenderView');
    }

    /**
     * Adds a parameter to the view with results built from all block collections.
     *
     * @todo Refactor out the collection result generation into a separate service
     *
     * @param \Netgen\BlockManager\Event\CollectViewParametersEvent $event
     */
    public function onRenderView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $flags = 0;
        if ($view->getContext() === ViewInterface::CONTEXT_API) {
            $flags = ResultSet::INCLUDE_UNKNOWN_ITEMS;
        }

        $collections = array();
        $pagers = array();

        foreach ($view->getBlock()->getCollectionReferences() as $collectionReference) {
            $pagerAdapter = new ResultBuilderAdapter(
                $this->resultBuilder,
                $collectionReference->getCollection(),
                $collectionReference->getOffset(),
                $flags
            );

            $pager = $this->buildPager($pagerAdapter, $collectionReference->getLimit());

            $collections[$collectionReference->getIdentifier()] = $pager->getCurrentPageResults();
            $pagers[$collectionReference->getIdentifier()] = $pager;
        }

        $event->addParameter('collections', $collections);
        $event->addParameter('pagers', $pagers);
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param int $limit
     *
     * @return \Pagerfanta\Pagerfanta
     */
    private function buildPager(AdapterInterface $adapter, $limit)
    {
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 ? $limit : $this->maxLimit);
        $pager->setCurrentPage(1);

        return $pager;
    }
}
