<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Zone;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\ZoneViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the zone in the provided view
 * is within a layout with the type specified in the configuration.
 */
final class LayoutType implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof ZoneViewInterface) {
            return false;
        }

        $layoutType = $view->getLayout()->getLayoutType();

        return in_array($layoutType->getIdentifier(), $config, true);
    }
}
