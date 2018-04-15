<?php

namespace Netgen\Bundle\BlockManagerBundle\Tests\Templating\Twig;

use Exception;
use Netgen\BlockManager\Core\Values\Layout\Layout;
use Netgen\BlockManager\Core\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutView;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class GlobalVariableTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $configMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $layoutResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $pageLayoutResolverMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $viewBuilderMock;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable
     */
    private $globalVariable;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);
        $this->layoutResolverMock = $this->createMock(LayoutResolverInterface::class);
        $this->pageLayoutResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->viewBuilderMock = $this->createMock(ViewBuilderInterface::class);

        $this->requestStack = new RequestStack();
        $this->requestStack->push(Request::create('/'));

        $this->globalVariable = new GlobalVariable(
            $this->configMock,
            $this->layoutResolverMock,
            $this->pageLayoutResolverMock,
            $this->viewBuilderMock,
            $this->requestStack
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate()
    {
        $this->pageLayoutResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $this->assertEquals(
            'pagelayout.html.twig',
            $this->globalVariable->getPageLayoutTemplate()
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayout()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will(
                $this->returnValue(
                    new LayoutView(array('layout' => new Layout()))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new Layout(), $this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will($this->returnValue(null));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutView()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will(
                $this->returnValue(
                    new LayoutView(array('layout' => new Layout()))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new LayoutView(array('layout' => new Layout())), $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithException()
    {
        $subRequest = Request::create('/');
        $subRequest->attributes->set('exception', new Exception());
        $this->requestStack->push($subRequest);

        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will(
                $this->returnValue(
                    new LayoutView(array('layout' => new Layout()))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new LayoutView(array('layout' => new Layout())), $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will($this->returnValue(null));

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertFalse($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRule()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->will(
                $this->returnValue(
                    new LayoutView(array('layout' => new Layout(), 'rule' => new Rule(array('layout' => new Layout()))))
                )
            );

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertEquals(new Rule(array('layout' => new Layout())), $this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will($this->returnValue(null));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        $this->assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolverExecuted()
    {
        $this->assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplate()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $layoutView = new LayoutView(array('layout' => new Layout()));
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo(new Layout()))
            ->will($this->returnValue($layoutView));

        $this->pageLayoutResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $this->assertEquals('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        $this->assertEquals(
            $layoutView,
            $this->requestStack->getCurrentRequest()->attributes->get('ngbmLayoutView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithAlreadyExistingResolvedLayout()
    {
        $layoutView = new LayoutView(array('layout' => new Layout()));
        $layoutView->setTemplate('layout.html.twig');

        $this->requestStack->getCurrentRequest()->attributes->set('ngbmLayoutView', $layoutView);

        $this->layoutResolverMock
            ->expects($this->never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->expects($this->at(0))
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $this->assertEquals('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        $this->assertEquals(
            $layoutView,
            $this->requestStack->getCurrentRequest()->attributes->get('ngbmLayoutView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithException()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will(
                $this->returnValue(
                    new Rule(array('layout' => new Layout()))
                )
            );

        $layoutView = new LayoutView(array('layout' => new Layout()));
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects($this->once())
            ->method('buildView')
            ->with($this->equalTo(new Layout()))
            ->will($this->returnValue($layoutView));

        $this->pageLayoutResolverMock
            ->expects($this->never())
            ->method('resolvePageLayout');

        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRequest->attributes->set('exception', new Exception());

        $this->assertEquals('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        $this->assertEquals($layoutView, $currentRequest->attributes->get('ngbmExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithExceptionWithAlreadyExistingResolvedLayout()
    {
        $layoutView = new LayoutView(array('layout' => new Layout()));
        $layoutView->setTemplate('layout.html.twig');

        $this->requestStack->getCurrentRequest()->attributes->set('ngbmExceptionLayoutView', $layoutView);

        $this->layoutResolverMock
            ->expects($this->never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects($this->never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->expects($this->at(0))
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $currentRequest = $this->requestStack->getCurrentRequest();
        $currentRequest->attributes->set('exception', new Exception());

        $this->assertEquals('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        $this->assertEquals($layoutView, $currentRequest->attributes->get('ngbmExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoResolvedRules()
    {
        $this->layoutResolverMock
            ->expects($this->once())
            ->method('resolveRule')
            ->will($this->returnValue(null));

        $this->pageLayoutResolverMock
            ->expects($this->once())
            ->method('resolvePageLayout')
            ->will($this->returnValue('pagelayout.html.twig'));

        $this->assertEquals(
            'pagelayout.html.twig',
            $this->globalVariable->getLayoutTemplate()
        );

        $this->assertFalse(
            $this->requestStack->getCurrentRequest()->attributes->get('ngbmLayoutView')
        );
    }

    /**
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\BlockManagerBundle\Templating\Twig\GlobalVariable::getConfig
     */
    public function testGetConfig()
    {
        $this->assertEquals($this->configMock, $this->globalVariable->getConfig());
    }
}
