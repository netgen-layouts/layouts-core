<?php

namespace Netgen\Bundle\BlockManagerBundle\Templating\Twig;

use Netgen\BlockManager\API\Values\Page\Layout;
use Netgen\BlockManager\Configuration\ConfigurationInterface;
use Netgen\BlockManager\Layout\Resolver\LayoutResolverInterface;
use Netgen\BlockManager\View\View\LayoutViewInterface;
use Netgen\BlockManager\View\ViewBuilderInterface;
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
     * Returns the pagelayout template.
     *
     * @return string
     */
    public function getPageLayoutTemplate()
    {
        return $this->pageLayoutResolver->resolvePageLayout();
    }

    /**
     * Returns the currently resolved layout.
     *
     * @return \Netgen\BlockManager\API\Values\Page\Layout
     */
    public function getLayout()
    {
        $this->resolveLayout();

        if (!$this->layoutView instanceof LayoutViewInterface) {
            return;
        }

        return $this->layoutView->getLayout();
    }

    /**
     * Returns the currently valid layout template, or base pagelayout if
     * no layout was resolved.
     *
     * @return string
     */
    public function getLayoutTemplate()
    {
        $this->resolveLayout();

        if (!$this->layoutView instanceof LayoutViewInterface) {
            return $this->getPageLayoutTemplate();
        }

        return $this->layoutView->getTemplate();
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
     * Resolves the used layout, based on current conditions.
     */
    protected function resolveLayout()
    {
        if ($this->layoutView instanceof LayoutViewInterface) {
            return;
        }

        foreach ($this->layoutResolver->resolveRules() as $rule) {
            if (!$rule->getLayout() instanceof Layout) {
                continue;
            }

            $this->layoutView = $this->viewBuilder->buildView($rule->getLayout());

            break;
        }
    }
}
