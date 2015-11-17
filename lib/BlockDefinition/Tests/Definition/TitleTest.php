<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Definition\Title;
use Netgen\BlockManager\BlockDefinition\Parameters;
use PHPUnit_Framework_TestCase;

class TitleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getIdentifier
     */
    public function testGetIdentifier()
    {
        $blockDefinition = new Title();

        self::assertEquals('title', $blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new Title();

        self::assertEquals(
            array(
                new Parameters\Select(
                    'tag',
                    'Tag',
                    array(
                        'options' => array(
                            'h1' => 'h1',
                            'h2' => 'h2',
                            'h3' => 'h3'
                        ),
                    ),
                    'h2'
                ),
                new Parameters\Text('title', 'Title', array(), 'Title'),
                new Parameters\Text('css_id', 'CSS ID'),
                new Parameters\Text('css_class', 'CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getValues
     */
    public function testGetValues()
    {
        $blockDefinition = new Title();

        self::assertEquals(array(), $blockDefinition->getValues());
    }
}
