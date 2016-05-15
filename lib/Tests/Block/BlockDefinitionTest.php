<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Tests\Block\Stubs\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints\NotBlank;

class BlockDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new BlockDefinition();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'css_id' => new Parameter\Text(),
                'css_class' => new Parameter\Text(),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getDynamicParameters
     */
    public function testGetDynamicParameters()
    {
        self::assertEquals(array(), $this->blockDefinition->getDynamicParameters(new Block()));
    }
}
