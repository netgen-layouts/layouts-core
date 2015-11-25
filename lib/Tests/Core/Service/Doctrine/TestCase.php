<?php

namespace Netgen\BlockManager\Tests\Core\Service\Doctrine;

use Netgen\BlockManager\Tests\Core\Persistence\Doctrine\TestCase as PersistenceTestCase;
use Netgen\BlockManager\Core\Service\LayoutService;
use Netgen\BlockManager\Core\Service\BlockService;
use PHPUnit_Framework_MockObject_MockObject;

trait TestCase
{
    use PersistenceTestCase;

    /**
     * Creates a layout service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $layoutValidatorMock
     *
     * @return \Netgen\BlockManager\API\Service\LayoutService
     */
    protected function createLayoutService(PHPUnit_Framework_MockObject_MockObject $layoutValidatorMock)
    {
        return new LayoutService(
            $layoutValidatorMock,
            $this->createLayoutHandler()
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $blockValidatorMock
     * @param \PHPUnit_Framework_MockObject_MockObject $layoutValidatorMock
     *
     * @return \Netgen\BlockManager\API\Service\BlockService
     */
    protected function createBlockService(
        PHPUnit_Framework_MockObject_MockObject $blockValidatorMock,
        PHPUnit_Framework_MockObject_MockObject $layoutValidatorMock
    ) {
        return new BlockService(
            $blockValidatorMock,
            $this->createLayoutService(
                $layoutValidatorMock
            ),
            $this->createBlockHandler()
        );
    }
}
