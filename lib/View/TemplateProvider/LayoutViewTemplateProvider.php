<?php

namespace Netgen\BlockManager\View\TemplateProvider;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\LayoutView;
use InvalidArgumentException;

class LayoutViewTemplateProvider implements ViewTemplateProvider
{
    /**
     * @var array
     */
    protected $config = array();

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * Provides a template for the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \InvalidArgumentException If there's no template defined for specified view
     *
     * @return string
     */
    public function provideTemplate(ViewInterface $view)
    {
        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $layout = $view->getLayout();
        $layoutIdentifier = $layout->getIdentifier();
        $context = $view->getContext();

        if (empty($this->config[$layoutIdentifier]['templates'][$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No template could be found for layout with identifier "%s"',
                    $layoutIdentifier,
                    $context
                )
            );
        }

        return $this->config[$layoutIdentifier]['templates'][$context];
    }

    /**
     * Returns if this view template provider supports the given view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function supports(ViewInterface $view)
    {
        return $view instanceof LayoutView;
    }
}
