<?php

namespace Netgen\BlockManager\Block;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Config\ConfigDefinitionAwareTrait;
use Netgen\BlockManager\Parameters\ParameterDefinitionCollectionTrait;

final class NullBlockDefinition implements BlockDefinitionInterface
{
    use ParameterDefinitionCollectionTrait;
    use ConfigDefinitionAwareTrait;

    public function getIdentifier()
    {
        return 'null';
    }

    public function getName()
    {
        return 'Invalid block definition';
    }

    public function getIcon()
    {
        return '';
    }

    public function isTranslatable()
    {
        return false;
    }

    public function getCollections()
    {
        return [];
    }

    public function hasCollection($identifier)
    {
        return false;
    }

    public function getCollection($identifier)
    {
    }

    public function getForms()
    {
        return [];
    }

    public function hasForm($formName)
    {
        return false;
    }

    public function getForm($formName)
    {
    }

    public function getViewTypes()
    {
        return [];
    }

    public function getViewTypeIdentifiers()
    {
        return [];
    }

    public function hasViewType($viewType)
    {
        return false;
    }

    public function getViewType($viewType)
    {
    }

    public function getDynamicParameters(Block $block)
    {
        return new DynamicParameters();
    }

    public function isContextual(Block $block)
    {
        return false;
    }

    public function hasPlugin($className)
    {
        return false;
    }

    public function isCacheable(Block $block)
    {
        return false;
    }
}
