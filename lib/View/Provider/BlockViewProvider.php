<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\View\View\BlockView;

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
        $blockView = new BlockView(
            array(
                'block' => $valueObject,
            )
        );

        $httpCacheConfig = $valueObject->getConfig('http_cache');

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
