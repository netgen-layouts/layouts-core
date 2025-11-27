<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\BlockView;

use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\Block\BlockDefinition\Handler\PagedCollectionsPlugin;
use Netgen\Layouts\Collection\Result\Pagerfanta\PagerFactory;
use Netgen\Layouts\Event\CollectViewParametersEvent;
use Netgen\Layouts\Event\LayoutsEvents;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use function in_array;
use function sprintf;

final class GetCollectionPagerListener implements EventSubscriberInterface
{
    /**
     * @param string[] $enabledContexts
     */
    public function __construct(
        private PagerFactory $pagerFactory,
        private RequestStack $requestStack,
        private array $enabledContexts,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [sprintf('%s.%s', LayoutsEvents::RENDER_VIEW, 'block') => 'onRenderView'];
    }

    /**
     * Adds a parameter to the view with results built from all block collections.
     */
    public function onRenderView(CollectViewParametersEvent $event): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest instanceof Request) {
            return;
        }

        $view = $event->view;
        if (!$view instanceof BlockViewInterface || !$view->hasParameter('collection_identifier')) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts, true)) {
            return;
        }

        $block = $view->getBlock();

        $collectionIdentifier = $view->getParameter('collection_identifier');

        $resultPager = $this->pagerFactory->getPager(
            $block->getCollection($collectionIdentifier),
            $currentRequest->query->getInt('page', 1),
            $this->getMaxPages($block),
        );

        $event->addParameter('collection', $resultPager->getCurrentPageResults());
        $event->addParameter('pager', $resultPager);
    }

    /**
     * Returns the maximum number of the pages for the provided block,
     * if paging is enabled and maximum number of pages is set for a block.
     */
    private function getMaxPages(Block $block): ?int
    {
        if (!$block->definition->hasHandlerPlugin(PagedCollectionsPlugin::class)) {
            return null;
        }

        if ($block->getParameter('paged_collections:enabled')->getValue() !== true) {
            return null;
        }

        return $block->getParameter('paged_collections:max_pages')->getValue();
    }
}
