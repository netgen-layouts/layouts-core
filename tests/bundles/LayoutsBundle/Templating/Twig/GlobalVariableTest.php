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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\UriSigner;

#[CoversClass(GlobalVariable::class)]
final class GlobalVariableTest extends TestCase
{
    private Stub&ConfigurationInterface $configStub;

    private Stub&LayoutResolverInterface $layoutResolverStub;

    private Stub&PageLayoutResolverInterface $pageLayoutResolverStub;

    private Stub&ViewBuilderInterface $viewBuilderStub;

    private Stub&UriSigner $uriSignerStub;

    private GlobalVariable $globalVariable;

    private Context $context;

    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->configStub = self::createStub(ConfigurationInterface::class);
        $this->layoutResolverStub = self::createStub(LayoutResolverInterface::class);
        $this->pageLayoutResolverStub = self::createStub(PageLayoutResolverInterface::class);
        $this->viewBuilderStub = self::createStub(ViewBuilderInterface::class);
        $this->uriSignerStub = self::createStub(UriSigner::class);

        $this->context = new Context();
        $this->requestStack = new RequestStack();

        $this->globalVariable = new GlobalVariable(
            $this->configStub,
            $this->layoutResolverStub,
            $this->pageLayoutResolverStub,
            $this->viewBuilderStub,
            $this->context,
            $this->uriSignerStub,
            $this->requestStack,
            true,
        );
    }

    public function testGetPageLayoutTemplate(): void
    {
        $this->pageLayoutResolverStub
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame(
            'pagelayout.html.twig',
            $this->globalVariable->getPageLayoutTemplate(),
        );
    }

    public function testGetLayout(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn(new LayoutView($layout));

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layout, $this->globalVariable->getLayout());
    }

    public function testGetLayoutWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(null);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayout());
    }

    public function testGetLayoutWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayout());
    }

    public function testGetLayoutView(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    public function testGetLayoutViewWithNoRequest(): void
    {
        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getLayoutView());
    }

    public function testGetLayoutViewWithException(): void
    {
        $subRequest = Request::create('/');
        $subRequest->attributes->set('exception', new Exception());
        $this->requestStack->push($subRequest);

        $layout = new Layout();
        $layoutView = new LayoutView($layout);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($layoutView, $this->globalVariable->getLayoutView());
    }

    public function testGetLayoutViewWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(null);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertFalse($this->globalVariable->getLayoutView());
    }

    public function testGetLayoutViewWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getLayoutView());
    }

    public function testGetRule(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $layout = new Layout();
        $rule = Rule::fromArray(['layout' => $layout]);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->addParameter('rule', $rule);

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertSame($rule, $this->globalVariable->getRule());
    }

    public function testGetRuleWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(null);

        // This will trigger layout resolver
        $this->globalVariable->getLayoutTemplate();

        self::assertNull($this->globalVariable->getRule());
    }

    public function testGetRuleWithNoResolverExecuted(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        self::assertNull($this->globalVariable->getRule());
    }

    public function testGetLayoutTemplate(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);
        $layout = new Layout();

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->template = 'layout.html.twig';

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    public function testGetLayoutTemplateWithLayoutOverride(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);
        $layout = new Layout();

        $layoutView = new LayoutView($layout);
        $layoutView->template = 'layout.html.twig';

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate(ViewInterface::CONTEXT_DEFAULT, $layout));

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    public function testGetLayoutTemplateWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->template = 'layout.html.twig';

        $request = Request::create('/');
        $request->attributes->set('nglLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->pageLayoutResolverStub
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglLayoutView'));
    }

    public function testGetLayoutTemplateWithNoRequest(): void
    {
        $this->pageLayoutResolverStub
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());
    }

    public function testGetLayoutTemplateWithException(): void
    {
        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $this->requestStack->push($request);

        $layout = new Layout();

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(Rule::fromArray(['layout' => $layout]));

        $layoutView = new LayoutView($layout);
        $layoutView->template = 'layout.html.twig';

        $this->viewBuilderStub
            ->method('buildView')
            ->willReturn($layoutView);

        self::assertSame('layout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglExceptionLayoutView'));
    }

    public function testGetLayoutTemplateWithExceptionWithAlreadyExistingResolvedLayout(): void
    {
        $layout = new Layout();
        $layoutView = new LayoutView($layout);
        $layoutView->template = 'layout.html.twig';

        $request = Request::create('/');
        $request->attributes->set('exception', new Exception());
        $request->attributes->set('nglExceptionLayoutView', $layoutView);
        $this->requestStack->push($request);

        $this->pageLayoutResolverStub
            ->method('resolvePageLayout')
            ->willReturn('pagelayout.html.twig');

        self::assertSame('pagelayout.html.twig', $this->globalVariable->getLayoutTemplate());

        self::assertSame($layoutView, $request->attributes->get('nglExceptionLayoutView'));
    }

    public function testGetLayoutTemplateWithNoResolvedRules(): void
    {
        $request = Request::create('/');
        $this->requestStack->push($request);

        $this->layoutResolverStub
            ->method('resolveRule')
            ->willReturn(null);

        $this->pageLayoutResolverStub
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

    public function testGetContext(): void
    {
        $this->context->set('foo', 'bar');

        self::assertSame(['foo' => 'bar'], $this->globalVariable->getContext());
    }

    public function testGetContextString(): void
    {
        $this->context->set('foo', 'bar');
        $this->context->set('bar', 'baz');

        $this->uriSignerStub
            ->method('sign')
            ->willReturn('?nglContext%5Bfoo%5D=bar&nglContext%5Bbar%5D=baz&_hash=signature');

        self::assertSame(
            'nglContext%5Bfoo%5D=bar&nglContext%5Bbar%5D=baz&_hash=signature',
            $this->globalVariable->getContextString(),
        );
    }

    public function testGetConfig(): void
    {
        self::assertSame($this->configStub, $this->globalVariable->getConfig());
    }

    public function testGetDebug(): void
    {
        self::assertTrue($this->globalVariable->getDebug());
    }
}
