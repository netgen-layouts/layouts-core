<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCase;
use Netgen\BlockManager\Tests\Core\Service\BlockMapperTest as BaseBlockMapperTest;

class BlockMapperTest extends BaseBlockMapperTest
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareHandlers();

        parent::setUp();
    }
}
