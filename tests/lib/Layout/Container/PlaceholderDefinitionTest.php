<?php

namespace Netgen\BlockManager\Tests\Layout\Container;

use Netgen\BlockManager\Layout\Container\PlaceholderDefinition;
use PHPUnit\Framework\TestCase;

class PlaceholderDefinitionTest extends TestCase
{
    /**
     * @var \Netgen\BlockManager\Layout\Container\PlaceholderDefinition
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
     * @covers \Netgen\BlockManager\Layout\Container\PlaceholderDefinition::getIdentifier
     */
    public function testGetIdentifier()
    {
        $this->assertEquals('left', $this->placeholderDefinition->getIdentifier());
    }
}
