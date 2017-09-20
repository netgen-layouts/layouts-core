<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\HttpCache\TaggerInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LayoutResponseListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\TaggerInterface
     */
    private $tagger;

    public function __construct(TaggerInterface $tagger)
    {
        $this->tagger = $tagger;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::RESPONSE => 'onKernelResponse');
    }

    /**
     * Tags the response with the data for layout provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $layoutView = $event->getRequest()->attributes->get('ngbmView');
        if (!$layoutView instanceof LayoutViewInterface) {
            return;
        }

        $this->tagger->tagLayout(
            $event->getResponse(),
            $layoutView->getLayout()
        );
    }
}
