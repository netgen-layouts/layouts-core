<?php

namespace Netgen\BlockManager\View\Matcher;

use Netgen\BlockManager\View\ViewInterface;

interface MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     *
     * @return bool
     */
    public function match(ViewInterface $view);

    /**
     * Sets the config to match against.
     *
     * @param array $config
     */
    public function setConfig(array $config);
}
