<?php

declare(strict_types=1);

namespace Netgen\Layouts\Utils\BackwardsCompatibility;

use Symfony\Component\EventDispatcher\Event as LegacyEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;

use function class_exists;

// Deprecated BC layer for Symfony 4.3 which deprecated Event class from EventDispatcher component.
// Remove when support for Symfony 3.4 and lower ends.

if (Kernel::VERSION_ID >= 40400) {
    abstract class Event extends ContractsEvent {}
} elseif (class_exists(LegacyEvent::class)) {
    abstract class Event extends LegacyEvent {}
}
