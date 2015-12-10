<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\BlockDefinition\Parameter;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        $blockDefinition = new BlockDefinition();

        self::assertEquals(
            array(
                'css_id' => array(new NotBlank()),
            ),
            $blockDefinition->getParameterConstraints()
        );
    }
}
