<?php

namespace Netgen\BlockManager\View\Matcher;

abstract class Matcher implements MatcherInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Sets the config to match against
     *
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }
}
