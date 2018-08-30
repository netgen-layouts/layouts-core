<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher\Zone;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\ZoneViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the zone in the provided view
 * has an identifier with value specified in the configuration.
 */
final class Identifier implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof ZoneViewInterface) {
            return false;
        }

        return in_array($view->getZone()->getIdentifier(), $config, true);
    }
}
