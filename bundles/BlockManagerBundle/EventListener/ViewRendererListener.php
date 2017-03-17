<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ViewRendererListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    protected $viewRenderer;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\View\RendererInterface $viewRenderer
     */
    public function __construct(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => array('onView', -255));
    }

    /**
     * Renders the view.
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
