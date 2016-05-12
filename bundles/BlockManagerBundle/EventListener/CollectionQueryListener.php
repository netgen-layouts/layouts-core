<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\CollectionService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Collection\Query;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class CollectionQueryListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\CollectionService
     */
    protected $collectionService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\CollectionService $collectionService
     */
    public function __construct(CollectionService $collectionService)
    {
        $this->collectionService = $collectionService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Must happen AFTER param converter listener
        return array(KernelEvents::CONTROLLER => array('onKernelController', -50));
    }

    /**
     * If both collection and query are present in the request,
     * checks if query belongs to the collection.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If query does not belong to the collection
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if (!$attributes->has('query') || !$attributes->has('collection')) {
            return;
        }

        $query = $attributes->get('query');
        $collection = $attributes->get('collection');

        if (!$query instanceof Query || !$collection instanceof Collection) {
            return;
        }

        if (!$this->collectionService->queryExists($collection, $query)) {
            throw new NotFoundException('query', $query->getId());
        }
    }
}
