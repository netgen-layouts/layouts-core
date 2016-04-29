<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\Definition\Paragraph;
use Netgen\BlockManager\BlockDefinition\Parameter;
use Netgen\BlockManager\Core\Values\Page\Block;
use Symfony\Component\Validator\Constraints;

class ParagraphTest extends \PHPUnit_Framework_TestCase
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
                'content' => new Parameter\Text('Content', true),
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(
            array(
                'content' => array(
                    new Constraints\NotBlank(),
                ),
            ),
            $blockDefinition->getParameterConstraints()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getValues
     */
    public function testGetValues()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(array(), $blockDefinition->getValues(new Block()));
    }
}
