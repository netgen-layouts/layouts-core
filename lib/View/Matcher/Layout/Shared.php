<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Layout;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\LayoutViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function array_values;
use function count;

/**
 * This matcher matches if the shared flag of the layout in the provided view
 * matches the value provided in the configuration.
 */
final class Shared implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof LayoutViewInterface) {
            return false;
        }

        if (count($config) === 0) {
            return true;
        }

        return $view->getLayout()->isShared() === array_values($config)[0];
    }
}
