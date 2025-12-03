<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event will be dispatched when the request is matched as being an admin interface request.
 */
final class AdminMatchEvent extends Event
{
    /**
     * Pagelayout template to be used by admin interface.
     */
    public ?string $pageLayoutTemplate = null;

    public function __construct(
        /**
         * The request the kernel is currently processing.
         */
        public private(set) Request $request,
        /**
         * The request type the kernel is currently processing.  One of
         * HttpKernelInterface::MAIN_REQUEST and HttpKernelInterface::SUB_REQUEST.
         */
        public private(set) int $requestType,
    ) {}
}
