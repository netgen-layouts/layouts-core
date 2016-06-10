<?php

namespace Netgen\BlockManager\Parameters\Parameter;

use Netgen\BlockManager\Parameters\Parameter;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class Number extends Parameter
{
    /**
     * Returns the parameter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'number';
    }

    /**
     * Configures the options for this parameter.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('min', null);
        $optionsResolver->setDefault('max', null);

        $optionsResolver->setRequired(array('min', 'max'));

        $optionsResolver->setAllowedTypes('min', array('numeric', 'null'));
        $optionsResolver->setAllowedTypes('max', array('numeric', 'null'));
    }

    /**
     * Returns constraints that are specific to parameter.
     *
     * @param array $groups
     *
     * @return \Symfony\Component\Validator\Constraint[]
     */
    public function getParameterConstraints(array $groups = null)
    {
        $groupOptions = $this->getBaseConstraintOptions($groups);

        $constraints = array(
            new Constraints\Type(
                array(
                    'type' => 'numeric',
                ) + $groupOptions
            ),
        );

        if ($this->options['min'] !== null) {
            $constraints[] = new Constraints\GreaterThanOrEqual(
                array('value' => $this->options['min']) + $groupOptions
            );
        }

        if ($this->options['max'] !== null) {
            $constraints[] = new Constraints\LessThanOrEqual(
                array('value' => $this->options['max']) + $groupOptions
            );
        }

        return $constraints;
    }
}
