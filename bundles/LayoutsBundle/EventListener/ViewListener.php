<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

final class ViewListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ViewEvent::class => 'onView'];
    }

    /**
     * Sets the Netgen Layouts view provided by the controller to the request.
     */
    public function onView(ViewEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ViewInterface) {
            return;
        }

        $event->getRequest()->attributes->set('nglView', $controllerResult);
    }
}
