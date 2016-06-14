<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Core\Service\Mapper\BlockMapperTest as BaseBlockMapperTest;

class BlockMapperTest extends BaseBlockMapperTest
{
    use TestCaseTrait;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->preparePersistence();

        parent::setUp();
    }

    public function tearDown()
    {
        $this->closeDatabaseConnection();
    }
}
