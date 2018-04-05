<?php

namespace Netgen\BlockManager\Tests\Block\Stubs;

use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\Block\BlockDefinitionInterface;
use Netgen\BlockManager\Parameters\ParameterCollectionTrait;

final class BlockDefinitionWithParameterDefinitions implements BlockDefinitionInterface
{
    use ParameterCollectionTrait;

    public function __construct(array $parameterDefinitions)
    {
        $this->parameterDefinitions = $parameterDefinitions;
    }

    public function getIdentifier()
    {
    }

    public function getName()
    {
    }

    public function getIcon()
    {
    }

    public function isTranslatable()
    {
    }

    public function getCollections()
    {
    }

    public function hasCollection($identifier)
    {
    }

    public function getCollection($identifier)
    {
    }

    public function getForms()
    {
    }

    public function hasForm($formName)
    {
    }

    public function getForm($formName)
    {
    }

    public function getViewTypes()
    {
    }

    public function getViewTypeIdentifiers()
    {
    }

    public function hasViewType($viewType)
    {
    }

    public function getViewType($viewType)
    {
    }

    public function getDynamicParameters(Block $block)
    {
    }

    public function isContextual(Block $block)
    {
    }

    public function getConfigDefinitions()
    {
    }

    public function hasPlugin($className)
    {
    }
}
