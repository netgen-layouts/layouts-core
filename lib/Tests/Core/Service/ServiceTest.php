<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry;
use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\CollectionValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;

abstract class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     * @param \Netgen\BlockManager\Configuration\Registry\LayoutTypeRegistry $layoutTypeRegistry
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    abstract protected function createLayoutService(LayoutValidator $validator, LayoutTypeRegistry $layoutTypeRegistry);

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService(BlockValidator $validator);

    /**
     * Creates a collection service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\CollectionValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\CollectionService
     */
    abstract protected function createCollectionService(CollectionValidator $validator);
}
