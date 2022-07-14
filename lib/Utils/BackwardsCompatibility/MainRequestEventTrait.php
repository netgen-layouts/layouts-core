<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\HttpKernel\Event\KernelEvent;

use function method_exists;

/**
 * Trait that supports is(Main|Master)Request in events.
 *
 * Remove when support for Symfony <5.3 ends.
 */
trait MainRequestEventTrait
{
    private function isMainRequest(KernelEvent $event): bool
    {
        if (method_exists($event, 'isMainRequest')) {
            return $event->isMainRequest();
        }

        return $event->isMasterRequest();
    }
}
