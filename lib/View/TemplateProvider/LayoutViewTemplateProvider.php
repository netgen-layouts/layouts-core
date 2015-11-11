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
        if (!$view instanceof LayoutView) {
            throw new InvalidArgumentException('Layout view template provider can only provide templates to layout views');
        }

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
}
