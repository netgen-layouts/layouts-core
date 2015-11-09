<?php

namespace Netgen\BlockManager\Core\Service\Tests;

use Netgen\BlockManager\Core\Values\Page\Block;

abstract class BlockServiceTest extends ServiceTest
{
    /**
     * @covers \Netgen\BlockManager\Core\Service\BlockService::__construct
     * @covers \Netgen\BlockManager\Core\Service\BlockService::loadBlock
     */
    public function testLoadBlock()
    {
        $blockService = $this->createBlockService();

        self::assertEquals(
            new Block(
                array(
                    'id' => 1,
                    'zoneId' => 2,
                    'definitionIdentifier' => 'paragraph',
                    'viewType' => 'default',
                    'parameters' => array(
                        'some_param' => 'some_value',
                    ),
                )
            ),
            $blockService->loadBlock(1)
        );
    }
}
