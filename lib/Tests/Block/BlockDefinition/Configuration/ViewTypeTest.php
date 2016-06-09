<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition\Configuration;

use Netgen\BlockManager\Block\BlockDefinition\Configuration\ItemViewType;
use Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType;

class ViewTypeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType
     */
    protected $viewType;

    public function setUp()
    {
        $this->viewType = new ViewType(
            'large',
            'Large',
            array(
                'standard' => new ItemViewType('standard', 'Standard'),
                'standard_with_intro' => new ItemViewType('standard_with_intro', 'Standard with intro'),
            )
        );
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

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewTypes
     */
    public function testGetItemViewTypes()
    {
        self::assertEquals(
            array(
                'standard' => new ItemViewType('standard', 'Standard'),
                'standard_with_intro' => new ItemViewType('standard_with_intro', 'Standard with intro'),
            ),
            $this->viewType->getItemViewTypes()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::hasItemViewType
     */
    public function testHasItemViewType()
    {
        self::assertTrue($this->viewType->hasItemViewType('standard'));
        self::assertFalse($this->viewType->hasItemViewType('unknown'));
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     */
    public function testGetItemViewType()
    {
        self::assertEquals(
            new ItemViewType('standard', 'Standard'),
            $this->viewType->getItemViewType('standard')
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Configuration\ViewType::getItemViewType
     * @expectedException \RuntimeException
     */
    public function testGetItemViewTypeThrowsRuntimeException()
    {
        $this->viewType->getItemViewType('unknown');
    }
}
