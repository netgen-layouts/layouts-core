<?php

namespace Netgen\BlockManager\View\TemplateProvider;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\BlockView;
use InvalidArgumentException;

class BlockViewTemplateProvider implements ViewTemplateProvider
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
        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $block = $view->getBlock();
        $definitionIdentifier = $block->getDefinitionIdentifier();
        $viewType = $block->getViewType();
        $context = $view->getContext();

        if (empty($this->config[$definitionIdentifier]['templates'][$viewType][$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No template could be found for block with "%s" identifier and "%s" view.',
                    $definitionIdentifier,
                    $viewType
                )
            );
        }

        return $this->config[$definitionIdentifier]['templates'][$viewType][$context];
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
        return $view instanceof BlockView;
    }
}
