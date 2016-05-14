<?php

namespace Netgen\BlockManager\Tests\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition\Title;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\Block\BlockDefinition\Title
     */
    protected $blockDefinition;

    /**
     * @var array
     */
    protected $options = array(
        'Heading 1' => 'h1',
        'Heading 2' => 'h2',
        'Heading 3' => 'h3',
    );

    public function setUp()
    {
        $this->blockDefinition = new Title();
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Title::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('title', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Title::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'tag' => new Parameter\Select(
                    array('options' => $this->options),
                    true
                ),
                'title' => new Parameter\Text(array(), true),
                'css_id' => new Parameter\Text(),
                'css_class' => new Parameter\Text(),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\Block\BlockDefinition\Title::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        self::assertEquals(
            array(
                'tag' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Choice(array('choices' => array_values($this->options))),
                ),
                'title' => array(
                    new Constraints\NotBlank(),
                ),
            ),
            $this->blockDefinition->getParameterConstraints()
        );
    }
}
