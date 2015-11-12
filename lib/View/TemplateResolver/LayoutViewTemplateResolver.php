<?php

namespace Netgen\BlockManager\View\TemplateResolver;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\LayoutView;
use InvalidArgumentException;

class LayoutViewTemplateResolver implements ViewTemplateResolver
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
     * Resolves a view template.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \InvalidArgumentException If there's no template defined for specified view
     *
     * @return string
     */
    public function resolveTemplate(ViewInterface $view)
    {
        /** @var \Netgen\BlockManager\View\LayoutViewInterface $view */
        $layout = $view->getLayout();
        $layoutIdentifier = $layout->getIdentifier();
        $context = $view->getContext();

        if (empty($this->config[$layoutIdentifier][$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No template could be found for layout with identifier "%s"',
                    $layoutIdentifier,
                    $context
                )
            );
        }

        return $this->config[$layoutIdentifier][$context];
    }

    /**
     * Returns if this template resolver supports the given view.
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
