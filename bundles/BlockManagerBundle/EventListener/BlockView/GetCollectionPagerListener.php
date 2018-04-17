<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GetCollectionPagerListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var array
     */
    private $enabledContexts;

    public function __construct(
        PagerFactory $pagerFactory,
        RequestStack $requestStack,
        array $enabledContexts
    ) {
        $this->pagerFactory = $pagerFactory;
        $this->requestStack = $requestStack;
        $this->enabledContexts = $enabledContexts;
    }

    public static function getSubscribedEvents()
    {
        return [BlockManagerEvents::RENDER_VIEW => 'onRenderView'];
    }

    /**
     * Adds a parameter to the view with results built from all block collections.
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

        $block = $view->getBlock();

        $currentPage = (int) $currentRequest->query->get('page', 1);
        $collectionIdentifier = $view->getParameter('collection_identifier');

        $resultPager = $this->pagerFactory->getPager(
            $block->getCollection($collectionIdentifier),
            $currentPage > 0 ? $currentPage : 1,
            $this->getMaxPages($block)
        );

        $event->addParameter('collection', $resultPager->getCurrentPageResults());
        $event->addParameter('pager', $resultPager);
    }

    /**
     * Returns the maximum number of the pages for the provided block,
     * if paging is enabled and maximum number of pages is set for a block.
     *
     * @param \Netgen\BlockManager\API\Values\Block\Block $block
     *
     * @return int|null
     */
    private function getMaxPages(Block $block)
    {
        if (!$block->getDefinition()->hasPlugin(PagedCollectionsPlugin::class)) {
            return;
        }

        if (!$block->getParameter('paged_collections:enabled')->getValue()) {
            return;
        }

        return $block->getParameter('paged_collections:max_pages')->getValue();
    }
}
