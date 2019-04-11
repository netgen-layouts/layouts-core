<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\ClientInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvalidationListener implements EventSubscriberInterface
{
    /**
     * @var \Netgen\Layouts\HttpCache\ClientInterface
     */
    private $httpCacheClient;

    public function __construct(ClientInterface $httpCacheClient)
    {
        $this->httpCacheClient = $httpCacheClient;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate',
            KernelEvents::EXCEPTION => 'onKernelException',
            ConsoleEvents::TERMINATE => 'onConsoleTerminate',
            ConsoleEvents::ERROR => 'onConsoleTerminate',
        ];
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onKernelTerminate(PostResponseEvent $event): void
    {
        $this->httpCacheClient->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $this->httpCacheClient->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onConsoleTerminate(ConsoleEvent $event): void
    {
        $this->httpCacheClient->commit();
    }
}
