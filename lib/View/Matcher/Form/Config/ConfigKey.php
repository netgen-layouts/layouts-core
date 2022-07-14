<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Form\Config;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\FormViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function in_array;

/**
 * This matcher matches if the form in the provided view
 * is used to edit the config with the config key equal to
 * value provided in the configuration.
 */
final class ConfigKey implements MatcherInterface
{
    public function match(ViewInterface $view, array $config): bool
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
