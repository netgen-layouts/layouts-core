<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilder;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GetCollectionPagerListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\Pagerfanta\ResultBuilder
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

    public function __construct(
        ResultBuilder $resultBuilder,
        RequestStack $requestStack,
        array $enabledContexts
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->requestStack = $requestStack;
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

        $resultPager = $this->resultBuilder->build(
            $view->getBlock()->getCollectionReference($collectionIdentifier)
        );

        $resultPager->setCurrentPage($currentPage);

        $event->addParameter('collection', $resultPager->getCurrentPageResults());
        $event->addParameter('pager', $resultPager);
    }
}
