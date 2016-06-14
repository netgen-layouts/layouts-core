<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\LayoutServiceTest as BaseLayoutServiceTest;

class LayoutServiceTest extends BaseLayoutServiceTest
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
