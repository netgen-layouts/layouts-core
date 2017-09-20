<?php

namespace Netgen\Bundle\BlockManagerAdminBundle\Tests\EventListener;

use Netgen\Bundle\BlockManagerAdminBundle\Event\AdminMatchEvent;
use Netgen\Bundle\BlockManagerAdminBundle\Event\BlockManagerAdminEvents;
use Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener;
use Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SetAdminPageLayoutListenerTest extends TestCase
{
    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener
     */
    private $listener;

    /**
     * @var \Netgen\Bundle\BlockManagerAdminBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    public function setUp()
    {
        $this->globalVariable = new GlobalVariable();

        $this->listener = new SetAdminPageLayoutListener(
            $this->globalVariable,
            'default.html.twig'
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener::__construct
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(BlockManagerAdminEvents::ADMIN_MATCH => array('onAdminMatch', -65535)),
            $this->listener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener::onAdminMatch
     */
    public function testOnAdminMatch()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngbm_admin_layout_resolver_index');

        $event = new AdminMatchEvent($request, HttpKernelInterface::MASTER_REQUEST);
        $this->listener->onAdminMatch($event);

        $this->assertEquals(
            'default.html.twig',
            $this->globalVariable->getPageLayoutTemplate()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerAdminBundle\EventListener\SetAdminPageLayoutListener::onAdminMatch
     */
    public function testOnAdminMatchWithExistingTemplate()
    {
        $request = Request::create('/');
        $request->attributes->set('_route', 'ngbm_admin_layout_resolver_index');

        $event = new AdminMatchEvent($request, HttpKernelInterface::MASTER_REQUEST);
        $event->setPageLayoutTemplate('template.html.twig');
        $this->listener->onAdminMatch($event);

        $this->assertEquals(
            'template.html.twig',
            $this->globalVariable->getPageLayoutTemplate()
        );
    }
}
