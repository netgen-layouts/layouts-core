<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class AdminMatchEvent extends Event
{
    /**
     * The request the kernel is currently processing.
     *
     * @var Request
     */
    protected $request;

    /**
     * The request type the kernel is currently processing.  One of
     * HttpKernelInterface::MASTER_REQUEST and HttpKernelInterface::SUB_REQUEST.
     *
     * @var int
     */
    protected $requestType;

    /**
     * Pagelayout template to be used by admin interface.
     *
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $requestType
     */
    public function __construct(Request $request, $requestType)
    {
        $this->request = $request;
        $this->requestType = $requestType;
    }

    /**
     * Returns the request the kernel is currently processing.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request type the kernel is currently processing.
     *
     * @return int
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Sets the pagelayout template which will be used for admin interface.
     *
     * @param string $template
     */
    public function setPageLayoutTemplate($template)
    {
        $this->pageLayoutTemplate = $template;
    }

    /**
     * Returns the pagelayout template which will be used for admin interface.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutTemplate;
    }
}
