<?php

namespace Netgen\BlockManager\BlockDefinition;

use Netgen\BlockManager\BlockDefinition\Parameter;
use Netgen\BlockManager\API\Values\Page\Block;
use Symfony\Component\Validator\Constraints;

class Title extends BlockDefinition
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
     * Returns block definition identifier.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'title';
    }

    /**
     * Returns the array specifying block parameters.
     *
     * @return \Netgen\BlockManager\BlockDefinition\Parameter[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new Parameter\Select(
                'Tag',
                true,
                array('options' => $this->options)
            ),
            'title' => new Parameter\Text('Title', true),
        ) + parent::getParameters();
    }

    /**
     * Returns the array specifying block parameter validator constraints.
     *
     * @return array
     */
    public function getParameterConstraints()
    {
        return array(
            'tag' => array(
                new Constraints\NotBlank(),
                new Constraints\Choice(array('choices' => array_values($this->options))),
            ),
            'title' => array(
                new Constraints\NotBlank(),
            ),
        ) + parent::getParameterConstraints();
    }

    /**
     * Returns the array of values provided by this block.
     *
     * @param \Netgen\BlockManager\API\Values\Page\Block $block
     * @param array $parameters
     *
     * @return array
     */
    public function getValues(Block $block, array $parameters = array())
    {
        return array();
    }
}
