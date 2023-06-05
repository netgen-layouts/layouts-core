<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Tests\Templating\Twig;

use Exception;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface;
use Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Layout\Resolver\LayoutResolverInterface;
use Netgen\Layouts\View\View\LayoutView;
use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\UriSigner;

final class GlobalVariableTest extends TestCase
{
    private MockObject $configMock;

    private MockObject $layoutResolverMock;

    private MockObject $pageLayoutResolverMock;

    private MockObject $viewBuilderMock;

    private MockObject $uriSignerMock;

    private GlobalVariable $globalVariable;

    private Context $context;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigurationInterface::class);
        $this->layoutResolverMock = $this->createMock(LayoutResolverInterface::class);
        $this->pageLayoutResolverMock = $this->createMock(PageLayoutResolverInterface::class);
        $this->viewBuilderMock = $this->createMock(ViewBuilderInterface::class);
        $this->uriSignerMock = $this->createMock(UriSigner::class);

        $this->context = new Context();
        $this->requestStack = new RequestStack();

        $this->globalVariable = new GlobalVariable(
            $this->configMock,
            $this->layoutResolverMock,
            $this->pageLayoutResolverMock,
            $this->viewBuilderMock,
            $this->context,
            $this->uriSignerMock,
            $this->requestStack,
            true,
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::__construct
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getPageLayoutTemplate
     */
    public function testGetPageLayoutTemplate(): void
    {
        $this->pageLayoutResolverMock
            ->expects(self::once())
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame(
            'pagelayout.html.twig',
            $this->globalVariable->getPageLayoutTemplate(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayout(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->willReturn(new LayoutView($layout));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layout, $this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(null);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayout
     */
    public function testGetLayoutWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayout());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutView(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoRequest(): void
    {
        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithException(): void
    {
        $subRequest = Request::create('/');
        $subRequest->attributes->set('exception', new Exception());
        $this->requestStack->push($subRequest);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(null);

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertFalse($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutView
     */
    public function testGetLayoutViewWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayoutView());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRule(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $rule = Rule::fromArray(['layout' => $layout]);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->addParameter('rule', $rule);

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($rule, $this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(null);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getRule
     */
    public function testGetRuleWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getRule());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplate(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);
        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(self::identicalTo($layout))
            ->willReturn($layoutView);

        $this->pageLayoutResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithLayoutOverride(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);
        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(self::identicalTo($layout))
            ->willReturn($layoutView);

        $this->pageLayoutResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate(ViewInterface::CONTEXT_DEFAULT, $layout));

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $request = Request::create('/');
        $request->attributes->set('nglLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoRequest(): void
    {
        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithException(): void
    {
        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $this->viewBuilderMock
            ->expects(self::once())
            ->method('buildView')
            ->with(self::identicalTo($layout))
            ->willReturn($layoutView);

        $this->pageLayoutResolverMock
            ->expects(self::never())
            ->method('resolvePageLayout');

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithExceptionWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->setTemplate('layout.html.twig');

        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $request->attributes->set('nglExceptionLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::never())
            ->method('resolveRule');

        $this->viewBuilderMock
            ->expects(self::never())
            ->method('buildView');

        $this->pageLayoutResolverMock
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglExceptionLayoutView'));
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::buildLayoutView
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getLayoutTemplate
     */
    public function testGetLayoutTemplateWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverMock
            ->expects(self::once())
            ->method('resolveRule')
            ->willReturn(null);

        $this->pageLayoutResolverMock
            ->expects(self::once())
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame(
            'pagelayout.html.twig',
            $this->globalVariable->getLayoutTemplate(),
        );

        self::assertFalse(
            $request->attributes->get('nglLayoutView'),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getContext
     */
    public function testGetContext(): void
    {
        $this->context->set('foo', 'bar');

        self::assertSame(['foo' => 'bar'], $this->globalVariable->getContext());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getContextString
     */
    public function testGetContextString(): void
    {
        $this->context->set('foo', 'bar');
        $this->context->set('bar', 'baz');

        $this->uriSignerMock
            ->method('sign')
            ->with('?nglContext%5Bfoo%5D=bar&nglContext%5Bbar%5D=baz')
            ->willReturn('?nglContext%5Bfoo%5D=bar&nglContext%5Bbar%5D=baz&_hash=signature');

        self::assertSame(
            'nglContext%5Bfoo%5D=bar&nglContext%5Bbar%5D=baz&_hash=signature',
            $this->globalVariable->getContextString(),
        );
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getConfig
     */
    public function testGetConfig(): void
    {
        self::assertSame($this->configMock, $this->globalVariable->getConfig());
    }

    /**
     * @covers \Netgen\Bundle\LayoutsBundle\Templating\Twig\GlobalVariable::getDebug
     */
    public function testGetDebug(): void
    {
        self::assertTrue($this->globalVariable->getDebug());
    }
}
