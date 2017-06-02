<?php

namespace Netgen\BlockManager\View\Matcher\Form\Config;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

class ConfigKey implements MatcherInterface
{
    /**
     * Returns if the view matches the config.
     *
     * @param \Netgen\BlockManager\View\ViewInterface $view
     * @param array $config
     *
     * @return bool
     */
    public function match(ViewInterface $view, array $config)
    {
        if (!$view instanceof FormViewInterface) {
            return false;
        }

        if (!$view->getForm()->getConfig()->hasOption('config_key')) {
            return false;
        }

        $configKey = $view->getForm()->getConfig()->getOption('config_key');

        return in_array($configKey, $config, true);
    }
}
