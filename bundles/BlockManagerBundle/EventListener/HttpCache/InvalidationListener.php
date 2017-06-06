<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use Netgen\BlockManager\HttpCache\ClientInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InvalidationListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\BlockManager\HttpCache\ClientInterface
     */
    protected $httpCacheClient;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\HttpCache\ClientInterface $httpCacheClient
     */
    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->httpCacheClient = $httpCacheClient;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => 'onKernelTerminate',
            KernelEvents::EXCEPTION => 'onKernelException',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
            ConsoleEvents::EXCEPTION => 'onConsoleTerminate',
        );
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $this->httpCacheClient->commit();
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->httpCacheClient->commit();
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleEvent $event
     */
    public function onConsoleTerminate(ConsoleEvent $event)
    {
        $this->httpCacheClient->commit();
    }
}
