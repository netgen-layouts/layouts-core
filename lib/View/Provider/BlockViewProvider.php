<?php

namespace Netgen\BlockManager\View\Provider;

use Netgen\BlockManager\API\Values\Page\Block;
use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\View\BlockView;

class BlockViewProvider implements ViewProviderInterface
{
    /**
     * @var \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface
     */
    protected $blockDefinitionRegistry;

    /**
     * Constructor.
     *
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     */
    public function __construct(BlockDefinitionRegistryInterface $blockDefinitionRegistry)
    {
        $this->blockDefinitionRegistry = $blockDefinitionRegistry;
    }

    /**
     * Provides the view.
     *
     * @param mixed $valueObject
     * @param array $parameters
     *
     * @return \Netgen\BlockManager\View\ViewInterface
     */
    public function provideView($valueObject, array $parameters = array())
    {
        /** @var \Netgen\BlockManager\API\Values\Page\Block $valueObject */
        $blockView = new BlockView(
            $valueObject,
            $this->blockDefinitionRegistry->getBlockDefinition(
                $valueObject->getDefinitionIdentifier()
            )
        );

        return $blockView;
    }

    /**
     * Returns if this view provider supports the given value object.
     *
     * @param mixed $valueObject
     *
     * @return bool
     */
    public function supports($valueObject)
    {
        return $valueObject instanceof Block;
    }
}
