<?php

namespace Netgen\Bundle\BlockManagerBundle\EventListener\HttpCache;

use FOS\HttpCache\Exception\ExceptionCollection;
use FOS\HttpCacheBundle\CacheManager;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InvalidationListener implements EventSubscriberInterface
{
    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    protected $cacheManager;

    /**
     * Constructor.
     *
     * @param \FOS\HttpCacheBundle\CacheManager $cacheManager
     */
    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
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
        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing
            // FOS Cache logger will log the errors
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        try {
            $this->cacheManager->flush();
        } catch (ExceptionCollection $e) {
            // Do nothing
            // FOS Cache logger will log the errors
        }
    }

    /**
     * @param \Symfony\Component\Console\Event\ConsoleEvent $event
     */
    public function onConsoleTerminate(ConsoleEvent $event)
    {
        $num = $this->cacheManager->flush();

        if ($num > 0 && OutputInterface::VERBOSITY_VERBOSE <= $event->getOutput()->getVerbosity()) {
            $event->getOutput()->writeln(sprintf('Sent %d invalidation request(s)', $num));
        }
    }
}
