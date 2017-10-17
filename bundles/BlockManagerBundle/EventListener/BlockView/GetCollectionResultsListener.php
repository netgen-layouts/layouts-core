<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilder;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class GetCollectionResultsListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilder
     */
    private $resultBuilder;

    /**
     * @var array
     */
    private $enabledContexts;

    public function __construct(ResultBuilder $resultBuilder, array $enabledContexts)
    {
        $this->resultBuilder = $resultBuilder;
        $this->enabledContexts = $enabledContexts;
    }

    public static function getSubscribedEvents()
    {
        return array(BlockManagerEvents::RENDER_VIEW => 'onRenderView');
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

        $collections = array();
        $pagers = array();

        foreach ($view->getBlock()->getCollectionReferences() as $collectionReference) {
            $pager = $this->resultBuilder->build($collectionReference, $flags);

            // In non AJAX scenarios, we're always rendering the first page of the collection
            // as specified by offset and limit in the collection itself
            $pager->setCurrentPage(1);

            $collections[$collectionReference->getIdentifier()] = $pager->getCurrentPageResults();
            $pagers[$collectionReference->getIdentifier()] = $pager;
        }

        $event->addParameter('collections', $collections);
        $event->addParameter('pagers', $pagers);
    }
}
