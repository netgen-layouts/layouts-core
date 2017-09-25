<?php

namespace Netgen\BlockManager\View\Matcher\Form\Config;

use Netgen\BlockManager\View\Matcher\MatcherInterface;
use Netgen\BlockManager\View\View\FormViewInterface;
use Netgen\BlockManager\View\ViewInterface;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the config with the config key equal to
 * value provided in the configuration.
 */
final class ConfigKey implements MatcherInterface
{
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
