<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\TaggerInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class LayoutResponseListener implements EventSubscriberInterface
{
    private bool $isExceptionResponse = false;

    public function __construct(
        private TaggerInterface $tagger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            ResponseEvent::class => ['onKernelResponse', 10],
            ExceptionEvent::class => 'onKernelException',
        ];
    }

    /**
     * Tags the response with the data for layout provided by the event.
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $attributeName = 'nglLayoutView';
        if ($request->attributes->has('nglOverrideLayoutView')) {
            $attributeName = 'nglOverrideLayoutView';
        } elseif ($this->isExceptionResponse) {
            $attributeName = 'nglExceptionLayoutView';
        }

        $layoutView = $request->attributes->get($attributeName);
        if (!$layoutView instanceof LayoutViewInterface) {
            return;
        }

        $this->tagger->tagLayout($layoutView->layout);
    }

    /**
     * Tags the exception response with the data for layout provided by the event.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->isExceptionResponse = true;
    }
}
