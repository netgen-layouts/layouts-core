<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Block\Block as APIBlock;
use Netgen\BlockManager\View\View\BlockView;
use Netgen\BlockManager\View\ViewInterface;

final class BlockViewProvider implements ViewProviderInterface
{
    public function provideView($value, array $parameters = []): ViewInterface
    {
        $blockView = new BlockView(
            [
                'block' => $value,
            ]
        );

        if ($value->hasConfig('http_cache')) {
            $httpCacheConfig = $value->getConfig('http_cache');

            $blockView->setIsCacheable(
                $httpCacheConfig->getParameter('use_http_cache')->getValue()
            );

            $blockView->setSharedMaxAge(
                $httpCacheConfig->getParameter('shared_max_age')->getValue()
            );
        }

        return $blockView;
    }

    public function supports($value): bool
    {
        return $value instanceof APIBlock;
    }
}
