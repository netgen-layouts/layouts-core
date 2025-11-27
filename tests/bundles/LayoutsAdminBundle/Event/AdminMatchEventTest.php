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
        self::assertSame($this->request, $this->event->request);
    }

    public function testGetRequestType(): void
    {
        self::assertSame($this->requestType, $this->event->requestType);
    }

    public function testGetPageLayoutTemplate(): void
    {
        self::assertNull($this->event->pageLayoutTemplate);
    }
}
