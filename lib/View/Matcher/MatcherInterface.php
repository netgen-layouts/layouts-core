<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

interface MatcherInterface
{
    /**
     * Returns if the view matches the config.
     */
    public function match(ViewInterface $view, array $config): bool;
}
