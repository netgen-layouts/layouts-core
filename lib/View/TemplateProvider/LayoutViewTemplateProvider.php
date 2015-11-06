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
     * Provides a template to the view.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @throws \InvalidArgumentException If there's no template defined for specified view
     */
    public function provideTemplate(ViewInterface $view)
    {
        if (!$view instanceof LayoutView) {
            return;
        }

        $layout = $view->getLayout();
        $layoutIdentifier = $layout->getIdentifier();
        $context = $view->getContext();

        if (!empty($this->config[$layoutIdentifier]["{$context}_template"])) {
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
