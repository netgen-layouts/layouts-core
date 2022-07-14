<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Zone;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\ZoneViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

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
