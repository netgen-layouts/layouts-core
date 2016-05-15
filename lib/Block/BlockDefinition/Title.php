<?php

namespace Netgen\BlockManager\Block\BlockDefinition;

use Netgen\BlockManager\Block\BlockDefinition;
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
     * @return \Netgen\BlockManager\Parameters\ParameterInterface[]
     */
    public function getParameters()
    {
        return array(
            'tag' => new Parameter\Select(
                array('options' => $this->options),
                true
            ),
            'title' => new Parameter\Text(array(), true),
        ) + parent::getParameters();
    }
}
