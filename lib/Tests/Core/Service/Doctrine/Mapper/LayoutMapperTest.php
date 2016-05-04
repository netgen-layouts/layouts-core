<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCase;
use Netgen\BlockManager\Tests\Core\Service\Mapper\LayoutMapperTest as BaseLayoutMapperTest;

class LayoutMapperTest extends BaseLayoutMapperTest
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        parent::setUp();
    }
}
