<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;

class ItemViewTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    protected $itemViewType;

    public function setUp()
    {
        $this->itemViewType = new ItemViewType('standard', 'Standard');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('standard', $this->itemViewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getName
     */
    public function testGetName()
    {
        self::assertEquals('Standard', $this->itemViewType->getName());
    }
}
