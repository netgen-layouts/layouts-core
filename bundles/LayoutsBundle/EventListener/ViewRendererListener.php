<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\Layouts\Error\ErrorHandlerInterface;
use Netgen\Layouts\View\ViewInterface;
use Netgen\Layouts\View\ViewRendererInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Throwable;

final class ViewRendererListener implements EventSubscriberInterface
{
    public function __construct(
        private ViewRendererInterface $viewRenderer,
        private ErrorHandlerInterface $errorHandler,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [ViewEvent::class => ['onView', -255]];
    }

    /**
     * Renders the view provided by the event.
     */
    public function onView(ViewEvent $event): void
    {
        $view = $event->getControllerResult();
        if (!$view instanceof ViewInterface) {
            return;
        }

        if (!$view->response instanceof Response) {
            return;
        }

        $renderedView = '';

        try {
            $renderedView = $this->viewRenderer->renderView($view);
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        $view->response->setContent($renderedView);
        $event->setResponse($view->response);
    }
}
