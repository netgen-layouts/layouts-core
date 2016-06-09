<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface;
use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;
use PHPUnit\Framework\TestCase;

abstract class ServiceTest extends TestCase
{
    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    abstract protected function createLayoutService(LayoutValidator $validator, LayoutTypeRegistryInterface $layoutTypeRegistry);

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistryInterface $layoutTypeRegistry
     * @param \Netgen\BlockManager\Block\Registry\BlockDefinitionRegistryInterface $blockDefinitionRegistry
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService(
        BlockValidator $validator,
        LayoutTypeRegistryInterface $layoutTypeRegistry,
        BlockDefinitionRegistryInterface $blockDefinitionRegistry
    );

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    abstract protected function createCollectionService(CollectionValidator $validator);

    /**
     * Creates a layout resolver service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutResolverValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutResolverService
     */
    abstract protected function createLayoutResolverService(LayoutResolverValidator $validator);
}
