<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Service\LayoutServiceTest as BaseLayoutServiceTest;

class LayoutServiceTest extends BaseLayoutServiceTest
{
    use TestCase;

    /**
     * Sets up the tests.
     */
    public function setUp()
    {
        $this->prepareServices();

        parent::setUp();
    }
}
