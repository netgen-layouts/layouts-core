<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\BlockView;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\Collection\ResultGeneratorInterface;
use Netgen\BlockManager\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Netgen\BlockManager\Event\View\CollectViewParametersEvent;
use Netgen\BlockManager\Event\View\ViewEvents;

class GetCollectionResultsListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\Collection\ResultGeneratorInterface
     */
    protected $resultGenerator;

    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * @var array
     */
    protected $enabledContexts;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Collection\ResultGeneratorInterface $resultGenerator
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     * @param array $enabledContexts
     */
    public function __construct(
        ResultGeneratorInterface $resultGenerator,
        BlockService $blockService,
        CollectionService $collectionService,
        array $enabledContexts = array()
    ) {
        $this->resultGenerator = $resultGenerator;
        $this->blockService = $blockService;
        $this->collectionService = $collectionService;
        $this->enabledContexts = $enabledContexts;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(ViewEvents::BUILD_VIEW => 'onBuildView');
    }

    /**
     * Includes results built from all block collections, if specified so.
     *
     * @param \Netgen\BlockManager\Event\View\CollectViewParametersEvent $event
     */
    public function onBuildView(CollectViewParametersEvent $event)
    {
        $view = $event->getView();
        if (!$view instanceof BlockViewInterface) {
            return;
        }

        if (!in_array($view->getContext(), $this->enabledContexts)) {
            return;
        }

        $results = array();

        $collectionReferences = $this->blockService->loadCollectionReferences($view->getBlock());
        foreach ($collectionReferences as $collectionReference) {
            $results[$collectionReference->getIdentifier()] = $this->resultGenerator->generateResult(
                $this->collectionService->loadCollection(
                    $collectionReference->getCollectionId(),
                    $collectionReference->getCollectionStatus()
                ),
                $collectionReference->getOffset(),
                $collectionReference->getLimit()
            );
        }

        $event->getParameterBag()->add(array('block_items' => $results));
    }
}
