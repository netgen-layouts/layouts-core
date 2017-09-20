<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use PHPUnit\Framework\TestCase;

class ItemViewTypeTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType
     */
    private $itemViewType;

    public function setUp()
    {
        $this->itemViewType = new ItemViewType(array('identifier' => 'standard', 'name' => 'Standard'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('standard', $this->itemViewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType::getName
     */
    public function testGetName()
    {
        $this->assertEquals('Standard', $this->itemViewType->getName());
    }
}
