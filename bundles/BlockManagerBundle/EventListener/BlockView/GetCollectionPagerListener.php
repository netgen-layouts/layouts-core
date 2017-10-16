<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilderAdapter;
use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GetCollectionPagerListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $enabledContexts;

    /**
     * @var int
     */
    private $maxLimit;

    public function __construct(
        ResultBuilderInterface $resultBuilder,
        RequestStack $requestStack,
        array $enabledContexts,
        $maxLimit
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->requestStack = $requestStack;
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
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $view = $event->getView();
        if (!$view instanceof BlockViewInterface || !$view->hasParameter('collection_identifier')) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $collectionIdentifier = $view->getParameter('collection_identifier');

        $currentPage = (int) $currentRequest->query->get('page', 1);
        $currentPage = $currentPage > 0 ? $currentPage : 1;

        $collectionReference = $view->getBlock()->getCollectionReference($collectionIdentifier);

        $pagerAdapter = new ResultBuilderAdapter(
            $this->resultBuilder,
            $collectionReference->getCollection(),
            $collectionReference->getOffset()
        );

        $pager = $this->buildPager(
            $pagerAdapter,
            $collectionReference->getLimit(),
            $currentPage
        );

        $event->addParameter('collection', $pager->getCurrentPageResults());
        $event->addParameter('pager', $pager);
    }

    /**
     * Builds the pager from provided adapter.
     *
     * @param \Pagerfanta\Adapter\AdapterInterface $adapter
     * @param int $limit
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    private function buildPager(AdapterInterface $adapter, $limit, $currentPage)
    {
        $pager = new Pagerfanta($adapter);

        $pager->setNormalizeOutOfRangePages(true);
        $pager->setMaxPerPage($limit > 0 ? $limit : $this->maxLimit);
        $pager->setCurrentPage($currentPage > 0 ? $currentPage : 1);

        return $pager;
    }
}
