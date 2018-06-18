<?php

declare(strict_types=1);

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\Event;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
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
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent
     */
    private $event;

    public function setUp(): void
    {
        $this->request = Request::create('/');
        $this->requestType = HttpKernelInterface::SUB_REQUEST;

        $this->event = new AdminMatchEvent(
            $this->request,
            $this->requestType
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::getRequest
     */
    public function testGetRequest(): void
    {
        $this->assertSame($this->request, $this->event->getRequest());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::getRequestType
     */
    public function testGetRequestType(): void
    {
        $this->assertSame($this->requestType, $this->event->getRequestType());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        $this->assertNull($this->event->getPageLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::getPageLayoutTemplate
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent::setPageLayoutTemplate
     */
    public function testSetPageLayoutTemplate(): void
    {
        $this->event->setPageLayoutTemplate('template.html.twig');

        $this->assertSame('template.html.twig', $this->event->getPageLayoutTemplate());
    }
}
