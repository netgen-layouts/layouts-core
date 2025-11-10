<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class AdminMatchEvent extends Event
{
    /**
     * Pagelayout template to be used by admin interface.
     */
    private ?string $pageLayoutTemplate = null;

    public function __construct(
        /**
         * The request the kernel is currently processing.
         */
        private Request $request,
        /**
         * The request type the kernel is currently processing.  One of
         * HttpKernelInterface::MAIN_REQUEST and HttpKernelInterface::SUB_REQUEST.
         */
        private int $requestType,
    ) {}

    /**
     * Returns the request the kernel is currently processing.
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Returns the request type the kernel is currently processing.
     */
    public function getRequestType(): int
    {
        return $this->requestType;
    }

    /**
     * Sets the pagelayout template which will be used for admin interface.
     */
    public function setPageLayoutTemplate(string $template): void
    {
        $this->pageLayoutTemplate = $template;
    }

    /**
     * Returns the pagelayout template which will be used for admin interface
     * or null if no template has been set.
     */
    public function getPageLayoutTemplate(): ?string
    {
        return $this->pageLayoutTemplate;
    }
}
