<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Tests\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameters;
use PHPUnit_Framework_TestCase;

class BlockDefinitionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new BlockDefinition();

        self::assertEquals(
            array(
                new Parameters\Text('css_id', 'CSS ID'),
                new Parameters\Text('css_class', 'CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }
}
