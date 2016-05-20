<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Core\Values\Page\Block;
use Netgen\BlockManager\Block\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;
use Netgen\BlockManager\Configuration\BlockDefinition\BlockDefinition as Config;

class BlockDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = $this->getMockForAbstractClass(BlockDefinition::class);
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

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition::setConfig
     * @covers \Netgen\BlockManager\Block\BlockDefinition::getConfig
     */
    public function testGetConfig()
    {
        $this->blockDefinition->setConfig(new Config('identifier', array(), array()));
        self::assertEquals(new Config('identifier', array(), array()), $this->blockDefinition->getConfig());
    }
}
