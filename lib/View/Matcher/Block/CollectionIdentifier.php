<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Block;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the block in the provided view
 * has a collection_identifier parameter with the provided value.
 */
final class CollectionIdentifier implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        if (!$view->hasParameter('collection_identifier')) {
            return false;
        }

        return in_array($view->getParameter('collection_identifier'), $config, true);
    }
}
