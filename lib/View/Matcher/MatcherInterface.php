<?php

declare(strict_types=1);

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

interface MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $config
     *
     * @return bool
     */
    public function match(ViewInterface $view, array $config);
}
