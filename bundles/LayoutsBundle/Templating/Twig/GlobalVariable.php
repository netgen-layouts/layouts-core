<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\Templating\Twig;

use Netgen\BlockManager\API\Values\Layout\Layout;
use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
 * they will extend `ngbm.layoutTemplate`), it might happen that sub-requests
 * call the layout resolving process again, which might overwrite the
 * already resolved layout due to different conditions, thus breaking
 * the main page, which still should be displayed in production environments
 * even if some of the sub-requests break.
 */
final class GlobalVariable
{
    /**
     * @var \Netgen\Bundle\LayoutsBundle\Configuration\ConfigurationInterface
     */
    private $configuration;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    private $layoutResolver;

    /**
     * @var \Netgen\Bundle\LayoutsBundle\Templating\PageLayoutResolverInterface
     */
    private $pageLayoutResolver;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    private $viewBuilder;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $pageLayoutTemplate;

    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        ConfigurationInterface $configuration,
        LayoutResolverInterface $layoutResolver,
        PageLayoutResolverInterface $pageLayoutResolver,
        ViewBuilderInterface $viewBuilder,
        RequestStack $requestStack,
        bool $debug
    ) {
        $this->configuration = $configuration;
        $this->layoutResolver = $layoutResolver;
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->viewBuilder = $viewBuilder;
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
     * @return \Netgen\BlockManager\View\View\LayoutViewInterface|false|null
     */
    public function getLayoutView()
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $masterRequest = $this->requestStack->getMasterRequest();

        if (!$currentRequest instanceof Request || !$masterRequest instanceof Request) {
            return null;
        }

        if ($masterRequest->attributes->has('ngbmExceptionLayoutView')) {
            if ($currentRequest !== $masterRequest || !$masterRequest->attributes->has('ngbmLayoutView')) {
                return $masterRequest->attributes->get('ngbmExceptionLayoutView');
            }
        }

        return $masterRequest->attributes->get('ngbmLayoutView');
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

        return $layoutView->getParameter('rule');
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
        $this->pageLayoutTemplate = $this->pageLayoutTemplate ?? $this->pageLayoutResolver->resolvePageLayout();

        return $this->pageLayoutTemplate;
    }

    /**
     * Returns the currently valid layout template, or base pagelayout if
     * no layout was resolved.
     */
    public function getLayoutTemplate(string $context = ViewInterface::CONTEXT_DEFAULT): ?string
    {
        $layoutView = $this->buildLayoutView($context);
        if (!$layoutView instanceof LayoutViewInterface) {
            return $this->getPageLayoutTemplate();
        }

        return $layoutView->getTemplate();
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
     * See class docs for more details.
     *
     * @return \Netgen\BlockManager\View\ViewInterface|false|null
     */
    private function buildLayoutView(string $context = ViewInterface::CONTEXT_DEFAULT)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $masterRequest = $this->requestStack->getMasterRequest();

        if (!$currentRequest instanceof Request || !$masterRequest instanceof Request) {
            return null;
        }

        if ($masterRequest->attributes->has('ngbmExceptionLayoutView')) {
            // After an exception layout is resolved, this case either means that
            // the main layout does not exist at all (because the error
            // happened before the rendering) or that it is already resolved
            // (if the error happened in subrequest), so this is a subsequent
            // call where we can safely return null in all cases.
            return null;
        }

        if (
            !$currentRequest->attributes->has('exception') &&
            $masterRequest->attributes->has('ngbmLayoutView')
        ) {
            // This is the case where we request the main layout more than once
            // within the regular page display, without the exception, so again
            // we return null.
            return null;
        }

        // Once we're here, we either request the main layout when there's no
        // exception, or the exception layout (both of those for the first time).

        $layoutView = false;

        $resolvedRule = $this->layoutResolver->resolveRule();
        if ($resolvedRule instanceof Rule) {
            $layoutView = $this->viewBuilder->buildView(
                $resolvedRule->getLayout(),
                $context,
                [
                    'rule' => $resolvedRule,
                ]
            );
        }

        $masterRequest->attributes->set(
            $currentRequest->attributes->has('exception') ?
                'ngbmExceptionLayoutView' :
                'ngbmLayoutView',
            $layoutView
        );

        return $layoutView;
    }
}
