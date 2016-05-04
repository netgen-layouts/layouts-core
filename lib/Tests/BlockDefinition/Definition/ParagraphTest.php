<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\Definition\Paragraph;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class ParagraphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Definition\Paragraph
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new Paragraph();
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('paragraph', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'content' => new Parameter\Text('Content', true),
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Paragraph::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        self::assertEquals(
            array(
                'content' => array(
                    new Constraints\NotBlank(),
                ),
            ),
            $this->blockDefinition->getParameterConstraints()
        );
    }
}
