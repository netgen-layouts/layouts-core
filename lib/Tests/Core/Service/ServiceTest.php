<?php

namespace Netgen\BlockManager\Tests\Core\Service;

use PHPUnit_Framework_MockObject_MockObject;

abstract class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Creates a layout service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $validatorMock
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    abstract protected function createLayoutService(
        PHPUnit_Framework_MockObject_MockObject $validatorMock
    );

    /**
     * Creates a block service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $validatorMock
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    abstract protected function createBlockService(
        PHPUnit_Framework_MockObject_MockObject $validatorMock
    );
}
