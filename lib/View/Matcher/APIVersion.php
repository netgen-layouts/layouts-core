<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the view has an `api_version`
 * parameter with the value specified in the configuration.
 */
final class APIVersion implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view->hasParameter('api_version')) {
            return false;
        }

        return in_array($view->getParameter('api_version'), $config, true);
    }
}
