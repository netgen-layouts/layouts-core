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
     * @param \PHPUnit_Framework_MockObject_MockObject $blockValidatorMock
     *
     * @return \Netgen\BlockManager\Core\Service\LayoutService
     */
    protected function createLayoutService(
        PHPUnit_Framework_MockObject_MockObject $layoutValidatorMock,
        PHPUnit_Framework_MockObject_MockObject $blockValidatorMock
    )
    {
        return new LayoutService(
            $layoutValidatorMock,
            $this->createBlockService(
                $blockValidatorMock
            ),
            $this->createLayoutHandler()
        );
    }

    /**
     * Creates a block service under test.
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $validatorMock
     *
     * @return \Netgen\BlockManager\Core\Service\BlockService
     */
    protected function createBlockService(
        PHPUnit_Framework_MockObject_MockObject $validatorMock
    ) {
        return new BlockService(
            $validatorMock,
            $this->createLayoutHandler(),
            $this->createBlockHandler()
        );
    }
}
