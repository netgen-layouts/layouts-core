<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewRendererListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\View\ViewRendererInterface
     */
    private $viewRenderer;

    public function __construct(ViewRendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => array('onView', -255));
    }

    /**
     * Renders the view provided by the event.
     *
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
     */
    public function onView(GetResponseForControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        if (!$controllerResult instanceof ViewInterface) {
            return;
        }

        $response = $controllerResult->getResponse();
        $response->setContent(
            $this->viewRenderer->renderView($controllerResult)
        );

        $event->setResponse($response);
    }
}
