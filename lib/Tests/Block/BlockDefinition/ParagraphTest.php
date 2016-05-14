<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Paragraph;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class ParagraphTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Paragraph
     */
    protected $blockDefinition;

    public function setUp()
    {
        $this->blockDefinition = new Paragraph();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Paragraph::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('paragraph', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Paragraph::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'content' => new Parameter\Text(array(), true),
                'css_id' => new Parameter\Text(),
                'css_class' => new Parameter\Text(),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Paragraph::getParameterConstraints
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
