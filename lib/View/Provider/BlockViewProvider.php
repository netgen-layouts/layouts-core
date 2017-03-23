<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
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

        $blockView = new BlockView(
            array(
                'block' => $block,
            )
        );

        $httpCacheConfig = $block->getConfig('http_cache');

        $blockView->setIsCacheable(
            $httpCacheConfig->getParameter('use_http_cache')->getValue()
        );

        $blockView->setSharedMaxAge(
            $httpCacheConfig->getParameter('shared_max_age')->getValue()
        );

        return $blockView;
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
