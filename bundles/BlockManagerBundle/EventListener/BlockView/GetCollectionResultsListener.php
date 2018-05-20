<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class GetCollectionResultsListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\PagerFactory
     */
    private $pagerFactory;

    /**
     * @var array
     */
    private $enabledContexts;

    public function __construct(PagerFactory $pagerFactory, array $enabledContexts)
    {
        $this->pagerFactory = $pagerFactory;
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

        $block = $view->getBlock();
        $collections = [];
        $pagers = [];

        foreach ($block->getCollections() as $identifier => $collection) {
            // In non AJAX scenarios, we're always rendering the first page of the collection
            // as specified by offset and limit in the collection itself
            $pager = $this->pagerFactory->getPager(
                $collection,
                1,
                $this->getMaxPages($block),
                $flags
            );

            $collections[$identifier] = $pager->getCurrentPageResults();
            $pagers[$identifier] = $pager;
        }

        $event->addParameter('collections', $collections);
        $event->addParameter('pagers', $pagers);
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
            return null;
        }

        if ($block->getParameter('paged_collections:enabled')->getValue() !== true) {
            return null;
        }

        return $block->getParameter('paged_collections:max_pages')->getValue();
    }
}
