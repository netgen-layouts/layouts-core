<?php

namespace Netgen\BlockManager\Tests\BlockDefinition;

use Netgen\BlockManager\BlockDefinition\Definition\Title;
use Netgen\BlockManager\BlockDefinition\Parameters;
use Netgen\BlockManager\Core\Values\Page\Block;
use Symfony\Component\Validator\Constraints;

class TitleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $options = array(
        'h1' => 'h1',
        'h2' => 'h2',
        'h3' => 'h3',
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
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameters
     */
    public function testGetParameters()
    {
        $blockDefinition = new Title();

        self::assertEquals(
            array(
                'tag' => new Parameters\Select(
                    'h2',
                    array('options' => $this->options)
                ),
                'title' => new Parameters\Text('Title'),
                'css_id' => new Parameters\Text(),
                'css_class' => new Parameters\Text(),
            ),
            $blockDefinition->getParameters()
        );
    }

    /**
     * @covers \Netgen\BlockManager\BlockDefinition\Definition\Title::getParameterNames
     */
    public function testGetParameterNames()
    {
        $blockDefinition = new Title();

        self::assertEquals(
            array(
                'tag' => 'Tag',
                'title' => 'Title',
                'css_id' => 'CSS ID',
                'css_class' => 'CSS class',
            ),
            $blockDefinition->getParameterNames()
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
                    new Constraints\Choice(array('choices' => $this->options)),
                ),
                'title' => array(
                    new Constraints\NotBlank(),
                ),
                'css_id' => false,
                'css_class' => false,
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
