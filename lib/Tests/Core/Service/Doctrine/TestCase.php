<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase as PersistenceTestCase;
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
