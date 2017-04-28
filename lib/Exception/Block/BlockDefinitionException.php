<?php

namespace Netgen\BlockManager\Exception\Block;

use InvalidArgumentException;
use Netgen\BlockManager\Exception\Exception;

class BlockDefinitionException extends InvalidArgumentException implements Exception
{
    /**
     * @param string $identifier
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     */
    public static function noBlockDefinition($identifier)
    {
        return new self(
            sprintf(
                'Block definition with "%s" identifier does not exist.',
                $identifier
            )
        );
    }

    /**
     * @param string $blockDefinition
     * @param string $collection
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     */
    public static function noCollection($blockDefinition, $collection)
    {
        return new self(
            sprintf(
                'Collection "%s" does not exist in "%s" block definition.',
                $collection,
                $blockDefinition
            )
        );
    }

    /**
     * @param string $blockDefinition
     * @param string $viewType
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     */
    public static function noViewType($blockDefinition, $viewType)
    {
        return new self(
            sprintf(
                'View type "%s" does not exist in "%s" block definition.',
                $viewType,
                $blockDefinition
            )
        );
    }

    /**
     * @param string $viewType
     * @param string $itemViewType
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     */
    public static function noItemViewType($viewType, $itemViewType)
    {
        return new self(
            sprintf(
                'Item view type "%s" does not exist in "%s" view type.',
                $itemViewType,
                $viewType
            )
        );
    }

    /**
     * @param string $blockDefinition
     * @param string $form
     *
     * @return \Netgen\BlockManager\Exception\Block\BlockDefinitionException
     */
    public static function noForm($blockDefinition, $form)
    {
        return new self(
            sprintf(
                'Form "%s" does not exist in "%s" block definition.',
                $form,
                $blockDefinition
            )
        );
    }
}
