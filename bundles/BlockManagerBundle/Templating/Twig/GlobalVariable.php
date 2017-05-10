<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GlobalVariable
{
    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface
     */
    protected $layoutResolver;

    /**
     * @var \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface
     */
    protected $pageLayoutResolver;

    /**
     * @var \Netgen\BlockManager\View\ViewBuilderInterface
     */
    protected $viewBuilder;

    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    protected $requestStack;

    /**
     * @var \Netgen\BlockManager\API\Values\Layout\Layout
     */
    protected $layout;

    /**
     * @var \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    protected $rule;

    /**
     * @var \Netgen\BlockManager\View\View\LayoutViewInterface
     */
    protected $layoutView;

    /**
     * @var string
     */
    protected $pageLayoutTemplate;

    /**
     * Constructor.
     *
     * @param \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $pageLayoutResolver
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        ConfigurationInterface $configuration,
        LayoutResolverInterface $layoutResolver,
        PageLayoutResolverInterface $pageLayoutResolver,
        ViewBuilderInterface $viewBuilder,
        RequestStack $requestStack
    ) {
        $this->configuration = $configuration;
        $this->layoutResolver = $layoutResolver;
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->viewBuilder = $viewBuilder;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns the currently resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Layout\Layout
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Returns the currently resolved layout view.
     *
     * @return \Netgen\BlockManager\View\View\LayoutViewInterface|bool
     */
    public function getLayoutView()
    {
        return $this->layoutView;
    }

    /**
     * Returns the rule used to resolve the current layout.
     *
     * @return \Netgen\BlockManager\API\Values\LayoutResolver\Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Returns the configuration object.
     *
     * @return \Netgen\Bundle\BlockManagerBundle\Configuration\ConfigurationInterface
     */
    public function getConfig()
    {
        return $this->configuration;
    }

    /**
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        if ($this->pageLayoutTemplate === null) {
            $this->pageLayoutTemplate = $this->pageLayoutResolver->resolvePageLayout();
        }

        return $this->pageLayoutTemplate;
    }

    /**
     * Returns the currently valid layout template, or base pagelayout if
     * no layout was resolved.
     *
     * @param string $context
     *
     * @return string
     */
    public function getLayoutTemplate($context = ViewInterface::CONTEXT_DEFAULT)
    {
        $this->buildLayoutView($context);

        if (!$this->layoutView instanceof LayoutViewInterface) {
            return $this->getPageLayoutTemplate();
        }

        return $this->layoutView->getTemplate();
    }

    /**
     * Resolves the used layout, based on current conditions.
     *
     * @param string $context
     */
    protected function buildLayoutView($context = ViewInterface::CONTEXT_DEFAULT)
    {
        $resolvedRules = $this->layoutResolver->resolveRules();
        if (empty($resolvedRules)) {
            $this->layoutView = false;

            return;
        }

        if (!$this->layoutView instanceof LayoutViewInterface) {
            $this->rule = $resolvedRules[0];
            $this->layout = $resolvedRules[0]->getLayout();

            $this->layoutView = $this->viewBuilder->buildView($this->layout, $context);

            $currentRequest = $this->requestStack->getCurrentRequest();
            if ($currentRequest instanceof Request) {
                $currentRequest->attributes->set('ngbmView', $this->layoutView);
            }
        }
    }
}
