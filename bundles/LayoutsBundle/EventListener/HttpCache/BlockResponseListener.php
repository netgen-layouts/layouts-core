<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Netgen\Layouts\View\View\BlockViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class BlockResponseListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    private TaggerInterface $tagger;

    public function __construct(TaggerInterface $tagger)
    {
        $this->tagger = $tagger;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', 10]];
    }

    /**
     * Tags the response with the data for block provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\ResponseEvent $event
     */
    public function onKernelResponse($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $blockView = $event->getRequest()->attributes->get('nglView');
        if (!$blockView instanceof BlockViewInterface) {
            return;
        }

        $this->tagger->tagBlock($blockView->getBlock());
    }
}
