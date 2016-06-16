<?php

namespace Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;

use Netgen\BlockManager\Parameters\FormMapper\ParameterHandler;
use Netgen\BlockManager\Parameters\ParameterInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Kernel;

class Choice extends ParameterHandler
{
    /**
     * @var array
     */
    protected $choicesAsValues;

    public function __construct()
    {
        // choices_as_values is deprecated on Symfony >= 3.1,
        // while on previous versions needs to be set to true
        $this->choicesAsValues = Kernel::VERSION_ID < 30100 ?
            array('choices_as_values' => true) :
            array();
    }

    /**
     * Returns the form type for the parameter.
     *
     * @return string
     */
    protected function getFormType()
    {
        return ChoiceType::class;
    }

    /**
     * Converts parameter options to Symfony form options.
     *
     * @param \Netgen\BlockManager\Parameters\ParameterInterface $parameter
     *
     * @return array
     */
    protected function convertOptions(ParameterInterface $parameter)
    {
        $parameterOptions = $parameter->getOptions();

        return array(
            'multiple' => $parameterOptions['multiple'],
            'choices' => is_callable($parameterOptions['options']) ?
                $parameterOptions['options']() :
                $parameterOptions['options'],
        ) + $this->choicesAsValues;
    }
}
