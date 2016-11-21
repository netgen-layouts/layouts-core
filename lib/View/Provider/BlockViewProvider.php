<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\TwigBlockDefinitionInterface;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\View\BlockView\TwigBlockView;
use Netgen\BlockManager\View\View\BlockView\ContextualizedTwigTemplate;

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
        $blockDefinition = $valueObject->getBlockDefinition();
        if (!$blockDefinition instanceof TwigBlockDefinitionInterface) {
            return new BlockView($valueObject);
        }

        $twigBlockContent = '';

        if (isset($parameters['twigTemplate']) && $parameters['twigTemplate'] instanceof ContextualizedTwigTemplate) {
            $twigBlockContent = $parameters['twigTemplate']->renderBlock(
                $blockDefinition->getTwigBlockName($valueObject)
            );
        }

        return new TwigBlockView($valueObject, $twigBlockContent);
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
        return $valueObject instanceof Block;
    }
}
