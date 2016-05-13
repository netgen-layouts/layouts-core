<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\API\Exception\NotFoundException;
use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Collection\Collection;
use Netgen\BlockManager\API\Values\Page\Block;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class BlockCollectionListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    protected $blockService;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\API\Service\BlockService $blockService
     */
    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
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
     * If both block and collection are present in the request,
     * checks if collection belongs to the block.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     *
     * @throws \Netgen\BlockManager\API\Exception\NotFoundException If collection does not belong to the block
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $attributes = $event->getRequest()->attributes;
        if (!$attributes->has('block') || !$attributes->has('collection')) {
            return;
        }

        $block = $attributes->get('block');
        $collection = $attributes->get('collection');

        if (!$block instanceof Block || !$collection instanceof Collection) {
            return;
        }

        $collectionReferences = $this->blockService->loadCollectionReferences($block);
        foreach ($collectionReferences as $collectionReference) {
            if ($collectionReference->getCollectionId() === $collection->getId()) {
                return;
            }
        }

        throw new NotFoundException('collection', $collection->getId());
    }
}
