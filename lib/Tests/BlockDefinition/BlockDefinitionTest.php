<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\BlockDefinition\Stubs\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlockDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition();
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        self::assertEquals(
            array(
                'css_id' => array(new NotBlank()),
            ),
            $this->blockDefinition->getParameterConstraints()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        self::assertEquals(array(), $this->blockDefinition->getDynamicParameters(new Block()));
    }
}
