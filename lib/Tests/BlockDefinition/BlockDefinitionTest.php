<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameters;

class BlockDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new BlockDefinition();

        self::assertEquals(
            array(
                'css_id' => new Parameters\Text(),
                'css_class' => new Parameters\Text(),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameterNames
     */
    public function testGetParameterNames()
    {
        $blockDefinition = new BlockDefinition();

        self::assertEquals(
            array(
                'css_id' => 'CSS ID',
                'css_class' => 'CSS class',
            ),
            $blockDefinition->getParameterNames()
        );
    }
}
