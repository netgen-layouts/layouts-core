<?php

namespace Netgen\BlockManager\Core\Service\Tests\Doctrine;

use Netgen\BlockManager\Core\Persistence\Doctrine\Tests\TestCase as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;

trait TestCase
{
    use PersistenceTestCase;

    /**
     * Creates a layout service under test.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    protected function createLayoutService()
    {
        return new LayoutService($this->createLayoutHandler());
    }

    /**
     * Creates a block service under test.
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    protected function createBlockService()
    {
        return new BlockService(
            $this->createLayoutService(),
            $this->createBlockHandler()
        );
    }
}
