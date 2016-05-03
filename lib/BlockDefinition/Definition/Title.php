<?php

namespace Netgen\BlockManager\BlockDefinition\Definition;

use Netgen\BlockManager\BlockDefinition\BlockDefinition;
use Netgen\BlockManager\Parameters\Parameter;
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
     * @return \Netgen\BlockManager\Parameters\Parameter[]
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
}
