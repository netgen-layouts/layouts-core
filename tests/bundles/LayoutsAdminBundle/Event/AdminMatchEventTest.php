<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Event;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class AdminMatchEventTest extends TestCase
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var int
     */
    private $requestType;

    /**
     * @var \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent
     */
    private $event;

    protected function setUp(): void
    {
        $this->request = Request::create('/');
        $this->requestType = HttpKernelInterface::SUB_REQUEST;

        $this->event = new AdminMatchEvent(
            $this->request,
            $this->requestType
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::__construct
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::getRequest
     */
    public function testGetRequest(): void
    {
        self::assertSame($this->request, $this->event->getRequest());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::getRequestType
     */
    public function testGetRequestType(): void
    {
        self::assertSame($this->requestType, $this->event->getRequestType());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        self::assertNull($this->event->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::getPageLayoutTemplate
     * @covers \Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent::setPageLayoutTemplate
     */
    public function testSetPageLayoutTemplate(): void
    {
        $this->event->setPageLayoutTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->event->getPageLayoutTemplate());
    }
}
