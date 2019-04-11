<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ViewListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    /**
     * Sets the Netgen Layouts view provided by the controller to the request.
     */
    public function onView(GetResponseForControllerResultEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ViewInterface) {
            return;
        }

        $event->getRequest()->attributes->set('nglView', $controllerResult);
    }
}
