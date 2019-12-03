<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher;

use Netgen\Layouts\View\ViewInterface;

interface MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param mixed[] $config
     */
    public function match(ViewInterface $view, array $config): bool;
}
