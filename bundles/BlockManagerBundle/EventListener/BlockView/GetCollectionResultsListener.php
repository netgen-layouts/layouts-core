<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\Collection\Result\ResultBuilderInterface;
use Netgen\BlockManager\Collection\Result\ResultSet;
use Netgen\BlockManager\Event\BlockManagerEvents;
use Netgen\BlockManager\Event\CollectViewParametersEvent;
use Netgen\BlockManager\View\View\BlockViewInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class GetCollectionResultsListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\Result\ResultBuilderInterface
     */
    private $resultBuilder;

    /**
     * @var int
     */
    private $maxLimit;

    /**
     * @var array
     */
    private $enabledContexts;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\Result\ResultBuilderInterface $resultBuilder
     * @param int $maxLimit
     * @param array $enabledContexts
     */
    public function __construct(
        ResultBuilderInterface $resultBuilder,
        $maxLimit,
        array $enabledContexts = array()
    ) {
        $this->resultBuilder = $resultBuilder;
        $this->maxLimit = $maxLimit;
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

        $collections = array();

        foreach ($view->getBlock()->getCollectionReferences() as $collectionReference) {
            $limit = $collectionReference->getLimit();
            if (empty($limit) || $limit > $this->maxLimit) {
                $limit = $this->maxLimit;
            }

            $collections[$collectionReference->getIdentifier()] = $this->resultBuilder->build(
                $collectionReference->getCollection(),
                $collectionReference->getOffset(),
                $limit,
                $view->getContext() === ViewInterface::CONTEXT_API ?
                    ResultSet::INCLUDE_UNKNOWN_ITEMS :
                    0
            );
        }

        $event->addParameter('collections', $collections);
    }
}
