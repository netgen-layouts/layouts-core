<?php

namespace Netgen\BlockManager\Tests\Block;

use Netgen\BlockManager\Block\PlaceholderDefinition;
use PHPUnit\Framework\TestCase;

class PlaceholderDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\PlaceholderDefinition
     */
    protected $placeholderDefinition;

    public function setUp()
    {
        $this->placeholderDefinition = new PlaceholderDefinition(
            array(
                'identifier' => 'left',
            )
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\PlaceholderDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('left', $this->placeholderDefinition->getIdentifier());
    }
}
