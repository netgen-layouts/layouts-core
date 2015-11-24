<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\BlockDefinition\Definition\Paragraph;
use Netgen\BlockManager\BlockDefinition\Parameters;
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
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getName
     */
    public function testGetName()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals('Paragraph', $blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(
            array(
                'content' => new Parameters\Text('Text'),
                'css_id' => new Parameters\Text(),
                'css_class' => new Parameters\Text(),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameterNames
     */
    public function testGetParameterNames()
    {
        $blockDefinition = new Paragraph();

        self::assertEquals(
            array(
                'content' => 'Content',
                'css_id' => 'CSS ID',
                'css_class' => 'CSS class',
            ),
            $blockDefinition->getParameterNames()
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
                'css_id' => false,
                'css_class' => false,
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
