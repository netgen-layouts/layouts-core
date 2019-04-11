<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Block;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\ViewInterface;

/**
 * This matcher matches if the block in the provided view
 * has a definition identifier with the value specified in the configuration.
 */
final class Definition implements MatcherInterface
{
    use DefinitionTrait;

    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        return $this->doMatch($view->getBlock(), $config);
    }
}
