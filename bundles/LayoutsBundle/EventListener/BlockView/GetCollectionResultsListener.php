<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\BlockView;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Collection\Result\ResultSet;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function in_array;
use function sprintf;

final class GetCollectionResultsListener implements EventSubscriberInterface
{
    private PagerFactory $pagerFactory;

    /**
     * @var string[]
     */
    private array $enabledContexts;

    /**
     * @param string[] $enabledContexts
     */
    public function __construct(PagerFactory $pagerFactory, array $enabledContexts)
    {
        $this->pagerFactory = $pagerFactory;
        $this->enabledContexts = $enabledContexts;
    }

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'];
    }

    /**
     * Adds a parameter to the view with results built from all block collections.
     */
    public function onRenderView(CollectViewParametersEvent $event): void
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $flags = 0;
        if ($view->getContext() === ViewInterface::CONTEXT_APP) {
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
                $flags,
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
     */
    private function getMaxPages(Block $block): ?int
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
