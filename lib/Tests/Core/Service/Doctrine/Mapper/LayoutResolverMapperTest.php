<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine\Mapper;

use Netgen\BlockManager\Tests\Core\Service\Doctrine\TestCaseTrait;
use Netgen\BlockManager\Tests\Core\Service\Mapper\LayoutResolverMapperTest as BaseLayoutResolverMapperTest;

class LayoutResolverMapperTest extends BaseLayoutResolverMapperTest
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
