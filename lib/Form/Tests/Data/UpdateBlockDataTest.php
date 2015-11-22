<?php

namespace Netgen\BlockManager\Form\Tests\Data;

use Netgen\BlockManager\Form\Data\UpdateBlockData;

class UpdateBlockDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\Form\Data\UpdateBlockData::__construct
     */
    public function testProperties()
    {
        $blockMock = $this->getMock('Netgen\BlockManager\API\Values\Page\Block');
        $updateStructMock = $this->getMock('Netgen\BlockManager\API\Values\BlockUpdateStruct');

        $data = new UpdateBlockData($blockMock, $updateStructMock);

        self::assertEquals($blockMock, $data->block);
        self::assertEquals($updateStructMock, $data->updateStruct);
    }
}
