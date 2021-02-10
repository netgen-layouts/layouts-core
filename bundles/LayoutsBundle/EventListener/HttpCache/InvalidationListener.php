<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\EventListener\HttpCache;

use Netgen\Layouts\HttpCache\InvalidatorInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class InvalidationListener implements EventSubscriberInterface
{
    private InvalidatorInterface $invalidator;

    public function __construct(InvalidatorInterface $invalidator)
    {
        $this->invalidator = $invalidator;
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
     *
     * @param \Symfony\Component\HttpKernel\Event\TerminateEvent $event
     */
    public function onKernelTerminate($event): void
    {
        $this->invalidator->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     *
     * @param \Symfony\Component\HttpKernel\Event\ExceptionEvent $event
     */
    public function onKernelException($event): void
    {
        $this->invalidator->commit();
    }

    /**
     * Commits all the collected invalidation requests.
     */
    public function onConsoleTerminate(ConsoleEvent $event): void
    {
        $this->invalidator->commit();
    }
}
