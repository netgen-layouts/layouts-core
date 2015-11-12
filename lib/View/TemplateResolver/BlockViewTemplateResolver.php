<?php

namespace Netgen\BlockManager\View\TemplateResolver;

use Netgen\BlockManager\View\ViewInterface;
use Netgen\BlockManager\View\BlockView;
use InvalidArgumentException;

class BlockViewTemplateResolver implements ViewTemplateResolver
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
        /** @var \Netgen\BlockManager\View\BlockViewInterface $view */
        $block = $view->getBlock();
        $definitionIdentifier = $block->getDefinitionIdentifier();
        $viewType = $block->getViewType();
        $context = $view->getContext();

        if (empty($this->config[$definitionIdentifier][$viewType][$context])) {
            throw new InvalidArgumentException(
                sprintf(
                    'No template could be found for block with "%s" identifier and "%s" view.',
                    $definitionIdentifier,
                    $viewType
                )
            );
        }

        return $this->config[$definitionIdentifier][$viewType][$context];
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
        return $view instanceof BlockView;
    }
}
