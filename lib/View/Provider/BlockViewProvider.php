<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\View\Twig\ContextualizedTwigTemplate;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\BlockView\Block;

class BlockViewProvider implements ViewProviderInterface
{
    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\Block\BlockDefinitionInterface $blockDefinition */
        $blockDefinition = $valueObject->getDefinition();
        $dynamicParameters = $blockDefinition->getDynamicParameters($valueObject, $parameters);

        $block = new Block($valueObject, $dynamicParameters);

        $viewParameters = array(
            'block' => $block,
        );

        if ($blockDefinition instanceof TwigBlockDefinitionInterface) {
            $viewParameters['twig_content'] = $this->getTwigBlockContent(
                $blockDefinition,
                $block,
                $parameters
            );
        }

        return new BlockView($viewParameters);
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof APIBlock;
    }

    /**
     * Returns the Twig block content from the provided block.
     *
     * @param \Netgen\BlockManager\Block\TwigBlockDefinitionInterface $blockDefinition
     * @param \Netgen\BlockManager\View\View\BlockView\Block $block
     * @param array $parameters
     *
     * @return string
     */
    protected function getTwigBlockContent(
        TwigBlockDefinitionInterface $blockDefinition,
        Block $block,
        array $parameters = array()
    ) {
        if (!isset($parameters['twig_template'])) {
            return '';
        }

        if (!$parameters['twig_template'] instanceof ContextualizedTwigTemplate) {
            return '';
        }

        return $parameters['twig_template']->renderBlock(
            $blockDefinition->getTwigBlockName($block)
        );
    }
}
