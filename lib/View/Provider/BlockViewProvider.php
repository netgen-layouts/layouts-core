<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block as APIBlock;
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
        $blockDefinition = $valueObject->getBlockDefinition();
        $dynamicParameters = $blockDefinition->getDynamicParameters($valueObject, $parameters);
        $block = new Block($valueObject, $dynamicParameters);

        return new BlockView(
            array(
                'valueObject' => $block,
                'parameters' => array(
                    'block' => $block,
                ),
            )
        );
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
}
