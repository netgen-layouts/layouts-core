<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\LayoutResolverServiceTest as BaseLayoutResolverServiceTest;

class LayoutResolverServiceTest extends BaseLayoutResolverServiceTest
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
