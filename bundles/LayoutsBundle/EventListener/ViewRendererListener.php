<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener;

use Netgen\BlockManager\Error\ErrorHandlerInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class ViewRendererListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    private $viewRenderer;

    /**
     * @var \Netgen\BlockManager\Error\ErrorHandlerInterface
     */
    private $errorHandler;

    public function __construct(ViewRendererInterface $viewRenderer, ErrorHandlerInterface $errorHandler)
    {
        $this->viewRenderer = $viewRenderer;
        $this->errorHandler = $errorHandler;
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::VIEW => ['onView', -255]];
    }

    /**
     * Renders the view provided by the event.
     */
    public function onView(GetResponseForControllerResultEvent $event): void
    {
        $view = $event->getControllerResult();
        if (!$view instanceof ViewInterface) {
            return;
        }

        $response = $view->getResponse();
        if (!$response instanceof Response) {
            return;
        }

        $renderedView = '';

        try {
            $renderedView = $this->viewRenderer->renderView($view);
        } catch (Throwable $t) {
            $this->errorHandler->handleError($t);
        }

        $response->setContent($renderedView);

        $event->setResponse($response);
    }
}
