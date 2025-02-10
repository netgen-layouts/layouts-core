<?php

declare(strict_types=1);

namespace Netgen\Layouts\Exception\Block;

use InvalidArgumentException;
use Netgen\Layouts\Exception\Exception;

use function sprintf;

final class BlockDefinitionException extends InvalidArgumentException implements Exception
{
    public static function noBlockDefinition(string $identifier): self
    {
        return new self(
            sprintf(
                'Block definition with "%s" identifier does not exist.',
                $identifier,
            ),
        );
    }

    public static function noCollection(string $blockDefinition, string $collection): self
    {
        return new self(
            sprintf(
                'Collection "%s" does not exist in "%s" block definition.',
                $collection,
                $blockDefinition,
            ),
        );
    }

    public static function noViewType(string $blockDefinition, string $viewType): self
    {
        return new self(
            sprintf(
                'View type "%s" does not exist in "%s" block definition.',
                $viewType,
                $blockDefinition,
            ),
        );
    }

    public static function noItemViewType(string $viewType, string $itemViewType): self
    {
        return new self(
            sprintf(
                'Item view type "%s" does not exist in "%s" view type.',
                $itemViewType,
                $viewType,
            ),
        );
    }

    public static function noForm(string $blockDefinition, string $form): self
    {
        return new self(
            sprintf(
                'Form "%s" does not exist in "%s" block definition.',
                $form,
                $blockDefinition,
            ),
        );
    }

    public static function noPlugin(string $identifier, string $pluginClass): self
    {
        return new self(
            sprintf(
                'Block definition with "%s" identifier does not have a plugin with "%s" class.',
                $identifier,
                $pluginClass,
            ),
        );
    }
}
