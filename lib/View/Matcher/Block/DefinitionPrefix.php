<?php

declare(strict_types=1);

namespace Netgen\Layouts\View\Matcher\Block;

use Netgen\Layouts\View\Matcher\MatcherInterface;
use Netgen\Layouts\View\View\BlockViewInterface;
use Netgen\Layouts\View\ViewInterface;

use function str_starts_with;

/**
 * This matcher matches if the block in the provided view has a definition
 * identifier that starts with the value specified in the configuration.
 */
final class DefinitionPrefix implements MatcherInterface
{
    use DefinitionTrait;

    public function match(ViewInterface $view, array $config): bool
    {
        if (!$view instanceof BlockViewInterface) {
            return false;
        }

        $identifier = $view->getBlock()->getDefinition()->getIdentifier();

        foreach ($config as $configItem) {
            if (str_starts_with($identifier, $configItem)) {
                return true;
            }
        }

        return false;
    }
}
