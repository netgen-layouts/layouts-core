<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener;

use Exception;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\ViewRendererInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

    public function __construct(ViewRendererInterface $viewRenderer, LoggerInterface $logger = null)
    {
        $this->viewRenderer = $viewRenderer;
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * Sets if debug is enabled or not.
     *
     * @param bool $debug
     */
    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
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

        try {
            $renderedView = $this->viewRenderer->renderView($controllerResult);
        } catch (Throwable $e) {
            $renderedView = $this->handleError($e, $controllerResult);
        } catch (Exception $e) {
            $renderedView = $this->handleError($e, $controllerResult);
        }

        $response = $controllerResult->getResponse();
        $response->setContent($renderedView);

        $event->setResponse($response);
    }

    /**
     * Handles the exception based on provided debug flag.
     *
     * @param \Throwable $throwable
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @todo Refactor out to separate service
     *
     * @deprecated Remove handling of exceptions in PHP 5.6 way
     *
     * @throws \Throwable
     *
     * @return string returns an empty string in non-debug mode
     */
    private function handleError(/* Throwable */ $throwable, ViewInterface $view)
    {
        $this->logger->critical('Error rendering a view', array('view' => $view, 'exception' => $throwable));

        if ($this->debug) {
            throw $throwable;
        }

        return '';
    }
}
