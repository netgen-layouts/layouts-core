<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Kernel;

final class EventDispatcherProxy
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function dispatch(object $event, string $eventName): ?object
    {
        if (Kernel::VERSION_ID >= 40300) {
            return $this->eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
