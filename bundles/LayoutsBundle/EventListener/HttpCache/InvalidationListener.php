<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

final class InvalidationListener implements EventSubscriberInterface
{
    public function __construct(
        private InvalidatorInterface $invalidator,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => 'onKernelTerminate',
            ExceptionEvent::class => 'onKernelException',
            ConsoleTerminateEvent::class => 'onConsoleTerminate',
            ConsoleErrorEvent::class => 'onConsoleError',
        ];
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        $this->invalidator->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $this->invalidator->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $this->invalidator->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onConsoleError(ConsoleErrorEvent $event): void
    {
        $this->invalidator->commit();
    }
}
