<?php

namespace Netgen\BlockManager\Tests\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\Definition\Title;
use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\Validator\Constraints;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\BlockManager\BlockDefinition\Definition\Title
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
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getIdentifier
     */
    public function testGetIdentifier()
    {
        self::assertEquals('title', $this->blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameters
     */
    public function testGetParameters()
    {
        self::assertEquals(
            array(
                'tag' => new Parameter\Select(
                    'Tag',
                    true,
                    array('options' => $this->options)
                ),
                'title' => new Parameter\Text('Title', true),
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $this->blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameterConstraints
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
