<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Tests\EventListener;

use Netgen\Bundle\LayoutsAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\LayoutsAdminBundle\Event\LayoutsAdminEvents;
use Netgen\Bundle\LayoutsAdminBundle\EventListener\SetAdminPageLayoutListener;
use Netgen\Bundle\LayoutsAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[CoversClass(SetAdminPageLayoutListener::class)]
final class SetAdminPageLayoutListenerTest extends TestCase
{
    private SetAdminPageLayoutListener $listener;

    private GlobalVariable $globalVariable;

    protected function setUp(): void
    {
        $this->globalVariable = new GlobalVariable('default.html.twig');

        $this->listener = new SetAdminPageLayoutListener($this->globalVariable);
    }

    public function testGetSubscribedEvents(): void
    {
        self::assertSame(
            [LayoutsAdminEvents::ADMIN_MATCH => ['onAdminMatch', -65535]],
            $this->listener::getSubscribedEvents(),
        );
    }

    public function testOnAdminMatch(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_admin_layout_resolver_index');

        $event = new AdminMatchEvent($request, HttpKernelInterface::MAIN_REQUEST);
        $this->listener->onAdminMatch($event);

        self::assertSame(
            'default.html.twig',
            $this->globalVariable->getPageLayoutTemplate(),
        );
    }

    public function testOnAdminMatchWithExistingTemplate(): void
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'nglayouts_admin_layout_resolver_index');

        $event = new AdminMatchEvent($request, HttpKernelInterface::MAIN_REQUEST);
        $event->setPageLayoutTemplate('template.html.twig');
        $this->listener->onAdminMatch($event);

        self::assertSame(
            'template.html.twig',
            $this->globalVariable->getPageLayoutTemplate(),
        );
    }
}
