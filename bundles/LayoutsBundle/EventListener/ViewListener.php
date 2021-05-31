<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\Layouts\Utils\BackwardsCompatibility\MainRequestEventTrait;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class ViewListener implements EventSubscriberInterface
{
    use MainRequestEventTrait;

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => 'onView'];
    }

    /**
     * Sets the Netgen Layouts view provided by the controller to the request.
     *
     * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
     */
    public function onView($event): void
    {
        if (!$this->isMainRequest($event)) {
            return;
        }

        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ViewInterface) {
            return;
        }

        $event->getRequest()->attributes->set('nglView', $controllerResult);
    }
}
