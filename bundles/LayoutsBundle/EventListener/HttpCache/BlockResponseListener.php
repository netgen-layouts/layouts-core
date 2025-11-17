<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class BlockResponseListener implements EventSubscriberInterface
{
    public function __construct(
        private TaggerInterface $tagger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => ['onKernelResponse', 10]];
    }

    /**
     * Tags the response with the data for block provided by the event.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $blockView = $event->getRequest()->attributes->get('nglView');
        if (!$blockView instanceof BlockViewInterface) {
            return;
        }

        $this->tagger->tagBlock($blockView->getBlock());
    }
}
