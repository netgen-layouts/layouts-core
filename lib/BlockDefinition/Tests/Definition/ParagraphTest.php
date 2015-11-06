<?php

namespace Netgen\BlockManager\BlockDefinition\Tests;

use Netgen\BlockManager\BlockDefinition\Definition\Paragraph;
use Netgen\BlockManager\BlockDefinition\Parameters;
use PHPUnit_Framework_TestCase;

class ParagraphTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getIdentifier
     */
    public function testGetIdentifier()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals('paragraph', $blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(
            array(
                new Parameters\Text('content', 'Content', null, 'Text'),
                new Parameters\Text('css_id', 'CSS ID'),
                new Parameters\Text('css_class', 'CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getValues
     */
    public function testGetValues()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(array(), $blockDefinition->getValues());
    }
}
