<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;

class ViewTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    protected $viewType;

    public function setUp()
    {
        $this->viewType = new ViewType('large', 'Large');
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::__construct
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('large', $this->viewType->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getName
     */
    public function testGetName()
    {
        self::assertEquals('Large', $this->viewType->getName());
    }
}
