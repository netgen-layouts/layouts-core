<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig;

use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface;
use Netgen\Layouts\API\Values\Layout\Layout;
use Netgen\Layouts\API\Values\LayoutResolver\Rule;
use Netgen\Layouts\Context\Context;
use Netgen\Layouts\Layout\Resolver\LayoutResolverInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewBuilderInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\UriSigner;

use function http_build_query;
use function mb_substr;
use function method_exists;

/**
 * This global variable injected into all templates serves two purposes.
 *
 * 1) It provides a convenient way to access the configuration object
 * 2) It provides the frontend templates with means to resolve layout
 *    and render its template.
 *
 * Resolving layouts with this global by calling `getLayoutTemplate()`
 * is only possible once per request for regular page, and once if
 * an exception happens. Subsequent calls will simply return the fallback
 * pagelayout.
 *
 * Reason for this is mainly exceptions that might happen in sub-requests,
 * while rendering blocks or block items. When an exception happens in
 * a sub-request, Symfony's ExceptionListener renders the exception,
 * but discards the rendered response and simply returns an empty response.
 * Since, usually, error templates will be Netgen Layouts enabled (meaning
 * they will extend `nglayouts.layoutTemplate`), it might happen that sub-requests
 * call the layout resolving process again, which might overwrite the
 * already resolved layout due to different conditions, thus breaking
 * the main page, which still should be displayed in production environments
 * even if some of the sub-requests break.
 */
final class GlobalVariable
{
    private ConfigurationInterface $configuration;

    private LayoutResolverInterface $layoutResolver;

    private PageLayoutResolverInterface $pageLayoutResolver;

    private ViewBuilderInterface $viewBuilder;

    private Context $context;

    private UriSigner $uriSigner;

    private RequestStack $requestStack;

    private bool $debug;

    private string $pageLayoutTemplate;

    public function __construct(
        ConfigurationInterface $configuration,
        LayoutResolverInterface $layoutResolver,
        PageLayoutResolverInterface $pageLayoutResolver,
        ViewBuilderInterface $viewBuilder,
        Context $context,
        UriSigner $uriSigner,
        RequestStack $requestStack,
        bool $debug
    ) {
        $this->configuration = $configuration;
        $this->layoutResolver = $layoutResolver;
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->viewBuilder = $viewBuilder;
        $this->context = $context;
        $this->uriSigner = $uriSigner;
        $this->requestStack = $requestStack;
        $this->debug = $debug;
    }

    /**
     * Returns the currently resolved layout view.
     *
     * Since the regular Symfony exceptions are rendered only in sub-requests,
     * we can return the resolved non-error layout for master requests even if the
     * exception layout is resolved too (that might happen if an error or exception
     * happened inside a user implemented sub-request, like rendering a block
     * item).
     *
     * In other words, we return the resolved exception layout only in case of a
     * sub-request (this case happens if an error/exception happens during the
     * rendering of the page) or in case of a master request if non-error layout
     * is NOT resolved (this case happens if an error/exception happens before
     * the rendering of the page).
     *
     * All other cases receive the non-error layout if it exists.
     *
     * @return \Netgen\Layouts\View\View\LayoutViewInterface|false|null
     */
    public function getLayoutView()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $mainRequest = method_exists($this->requestStack, 'getMainRequest') ?
            $this->requestStack->getMainRequest() :
            // Deprecated since Symfony 5.3
            $this->requestStack->getMasterRequest();

        if (!$currentRequest instanceof Request || !$mainRequest instanceof Request) {
            return null;
        }

        if ($mainRequest->attributes->has('nglOverrideLayoutView')) {
            return $mainRequest->attributes->get('nglOverrideLayoutView');
        }

        if ($mainRequest->attributes->has('nglExceptionLayoutView')) {
            if ($currentRequest !== $mainRequest || !$mainRequest->attributes->has('nglLayoutView')) {
                return $mainRequest->attributes->get('nglExceptionLayoutView');
            }
        }

        return $mainRequest->attributes->get('nglLayoutView');
    }

    /**
     * Returns the currently resolved layout or null if no layout was resolved.
     */
    public function getLayout(): ?Layout
    {
        $layoutView = $this->getLayoutView();
        if (!$layoutView instanceof LayoutViewInterface) {
            return null;
        }

        return $layoutView->getLayout();
    }

    /**
     * Returns the rule used to resolve the current layout or null if no layout was resolved.
     */
    public function getRule(): ?Rule
    {
        $layoutView = $this->getLayoutView();
        if (!$layoutView instanceof LayoutViewInterface) {
            return null;
        }

        return $layoutView->hasParameter('rule') ?
            $layoutView->getParameter('rule') :
            null;
    }

    /**
     * Returns the configuration object.
     */
    public function getConfig(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * Returns the pagelayout template.
     */
    public function getPageLayoutTemplate(): string
    {
        $this->pageLayoutTemplate ??= $this->pageLayoutResolver->resolvePageLayout();

        return $this->pageLayoutTemplate;
    }

    /**
     * Returns the currently valid layout template, or base pagelayout if
     * no layout was resolved.
     *
     * If $layout was provided, that specific layout will be used when building the view,
     * instead of resolving the template.
     */
    public function getLayoutTemplate(string $context = ViewInterface::CONTEXT_DEFAULT, ?Layout $layout = null): ?string
    {
        $layoutView = $this->buildLayoutView($context, $layout);
        if (!$layoutView instanceof LayoutViewInterface) {
            return $this->getPageLayoutTemplate();
        }

        return $layoutView->getTemplate();
    }

    /**
     * Returns the value of context in Netgen Layouts.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context->all();
    }

    /**
     * Returns the value of context in Netgen Layouts as a signed query string.
     */
    public function getContextString(): string
    {
        $signedQueryString = $this->uriSigner->sign(
            '?' . http_build_query(['nglContext' => $this->context->all()]),
        );

        return mb_substr($signedQueryString, 1);
    }

    /**
     * Returns if debug mode is activated in Netgen Layouts.
     */
    public function getDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Resolves the used layout, based on current conditions.
     *
     * Only resolves and returns the layout view once per request for regular page,
     * and once if an exception happens. Subsequent calls will simply return null.
     *
     * Also allows completely overriding the layout view by using a special request
     * attribute. This is useful when outside sources need to control the displayed
     * layout, like preview mechanism and so on.
     *
     * If $layout was provided, that specific layout will be used when building the view,
     * instead of resolving the template.
     *
     * See class docs for more details.
     *
     * @return \Netgen\Layouts\View\ViewInterface|false|null
     */
    public function buildLayoutView(string $context = ViewInterface::CONTEXT_DEFAULT, ?Layout $layout = null)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $mainRequest = method_exists($this->requestStack, 'getMainRequest') ?
            $this->requestStack->getMainRequest() :
            // Deprecated since Symfony 5.3
            $this->requestStack->getMasterRequest();

        if (!$currentRequest instanceof Request || !$mainRequest instanceof Request) {
            return null;
        }

        if ($mainRequest->attributes->has('nglOverrideLayoutView')) {
            return $currentRequest->attributes->get('nglOverrideLayoutView');
        }

        if ($mainRequest->attributes->has('nglExceptionLayoutView')) {
            // After an exception layout is resolved, this case either means that
            // the main layout does not exist at all (because the error
            // happened before the rendering) or that it is already resolved
            // (if the error happened in sub-request), so this is a subsequent
            // call where we can safely return null in all cases.
            return null;
        }

        if (
            !$currentRequest->attributes->has('exception')
            && $mainRequest->attributes->has('nglLayoutView')
        ) {
            // This is the case where we request the main layout more than once
            // within the regular page display, without the exception, so again
            // we return null.
            return null;
        }

        // Once we're here, we either request the main layout when there's no
        // exception, or the exception layout (both of those for the first time).

        $layoutView = false;
        $usedLayout = $layout;
        $viewParams = [];

        if (!$usedLayout instanceof Layout) {
            $resolvedRule = $this->layoutResolver->resolveRule($currentRequest);
            if ($resolvedRule instanceof Rule) {
                $usedLayout = $resolvedRule->getLayout();
                $viewParams = ['rule' => $resolvedRule];
            }
        }

        if ($usedLayout instanceof Layout) {
            $layoutView = $this->viewBuilder->buildView($usedLayout, $context, $viewParams);
        }

        $mainRequest->attributes->set(
            $currentRequest->attributes->has('exception') ?
                'nglExceptionLayoutView' :
                'nglLayoutView',
            $layoutView,
        );

        return $layoutView;
    }
}
