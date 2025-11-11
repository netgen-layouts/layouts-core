<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\Event;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(AdminMatchEvent::class)]
final class AdminMatchEventTest extends TestCase
{
    private Request $request;

    private int $requestType;

    private AdminMatchEvent $event;

    protected function setUp(): void
    {
        $this->request = Request::create('/');
        $this->requestType = HttpKernelInterface::SUB_REQUEST;

        $this->event = new AdminMatchEvent(
            $this->request,
            $this->requestType,
        );
    }

    public function testGetRequest(): void
    {
        self::assertSame($this->request, $this->event->getRequest());
    }

    public function testGetRequestType(): void
    {
        self::assertSame($this->requestType, $this->event->getRequestType());
    }

    public function testGetPageLayoutTemplate(): void
    {
        self::assertNull($this->event->getPageLayoutTemplate());
    }

    public function testSetPageLayoutTemplate(): void
    {
        $this->event->setPageLayoutTemplate('template.html.twig');

        self::assertSame('template.html.twig', $this->event->getPageLayoutTemplate());
    }
}
