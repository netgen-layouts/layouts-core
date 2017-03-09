<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\API\Values\LayoutResolver\Rule;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
use Netgen\BlockManager\View\ViewInterface;
use Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface;

class GlobalVariable
{
    /**
     * @var \Netgen\BlockManager\Configuration\ConfigurationInterface
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
     * @param \Netgen\BlockManager\Configuration\ConfigurationInterface $configuration
     * @param \Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface $layoutResolver
     * @param \Netgen\Bundle\BlockManagerBundle\Templating\PageLayoutResolverInterface $pageLayoutResolver
     * @param \Netgen\BlockManager\View\ViewBuilderInterface $viewBuilder
     */
    public function __construct(
        ConfigurationInterface $configuration,
        LayoutResolverInterface $layoutResolver,
        PageLayoutResolverInterface $pageLayoutResolver,
        ViewBuilderInterface $viewBuilder
    ) {
        $this->configuration = $configuration;
        $this->layoutResolver = $layoutResolver;
        $this->pageLayoutResolver = $pageLayoutResolver;
        $this->viewBuilder = $viewBuilder;
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
     * @return \Netgen\BlockManager\View\View\LayoutViewInterface
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
     * @return \Netgen\BlockManager\Configuration\ConfigurationInterface
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
        $resolvedRule = $this->layoutResolver->resolveRule();
        if (!$resolvedRule instanceof Rule) {
            return;
        }

        if ($this->layoutView === null) {
            $this->rule = $resolvedRule;
            $this->layout = $resolvedRule->getLayout();

            $this->layoutView = $this->viewBuilder->buildView($this->layout, $context);
        }
    }
}
