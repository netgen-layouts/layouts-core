<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\BlockDefinition\Definition\Title;
use Netgen\BlockManager\BlockDefinition\Parameter;
use Netgen\BlockManager\Core\Values\Page\Block;
use Symfony\Component\Validator\Constraints;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $options = array(
        'Heading 1' => 'h1',
        'Heading 2' => 'h2',
        'Heading 3' => 'h3',
    );

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getIdentifier
     */
    public function testGetIdentifier()
    {
        $blockDefinition = new Title();

        self::assertEquals('title', $blockDefinition->getIdentifier());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getName
     */
    public function testGetName()
    {
        $blockDefinition = new Title();

        self::assertEquals('Title', $blockDefinition->getName());
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new Title();

        self::assertEquals(
            array(
                'tag' => new Parameter\Select(
                    'Tag',
                    array('options' => $this->options)
                ),
                'title' => new Parameter\Text('Title'),
                'css_id' => new Parameter\Text('CSS ID'),
                'css_class' => new Parameter\Text('CSS class'),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameterConstraints
     */
    public function testGetParameterConstraints()
    {
        $blockDefinition = new Title();

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
            $blockDefinition->getParameterConstraints()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getValues
     */
    public function testGetValues()
    {
        $blockDefinition = new Title();

        self::assertEquals(array(), $blockDefinition->getValues(new Block()));
    }
}
