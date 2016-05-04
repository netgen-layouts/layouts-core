<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use Netgen\BlockManager\Core\Service\Validator\BlockValidator;
use Netgen\BlockManager\Core\Service\Validator\LayoutValidator;

abstract class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a layout service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\LayoutValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    abstract protected function createLayoutService(LayoutValidator $validator);

    /**
     * Creates a block service under test.
     *
     * @param \Netgen\BlockManager\Core\Service\Validator\BlockValidator $validator
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService(BlockValidator $validator);
}
