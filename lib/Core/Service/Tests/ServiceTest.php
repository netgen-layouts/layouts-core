<?php

namespace Netgen\BlockManager\Core\Service\Tests;

use PHPUnit_Framework_TestCase;

abstract class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Creates a layout service under test.
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    abstract protected function createLayoutService();

    /**
     * Creates a block service under test.
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService();
}
