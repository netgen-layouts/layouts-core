<?php

namespace Netgen\BlockManager\Parameters;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TranslatableParameterBuilder extends ParameterBuilder
{
    /**
     * Configures the parameter options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $optionsResolver
     */
    protected function configureOptions(OptionsResolver $optionsResolver)
    {
        $optionsResolver->setDefault('translatable', true);
        $optionsResolver->setRequired('translatable');
        $optionsResolver->setAllowedTypes('translatable', 'bool');

        $optionsResolver->setAllowedValues(
            'translatable',
            function ($value) {
                if (!$this->parentBuilder instanceof ParameterBuilderInterface) {
                    return true;
                }

                if (!$this->parentBuilder->getType() instanceof CompoundParameterTypeInterface) {
                    return true;
                }

                if ($value !== $this->parentBuilder->getOption('translatable')) {
                    if ($value) {
                        throw new InvalidOptionsException(
                            sprintf(
                                'Parameter "%s" cannot be translatable, since its parent parameter "%s" is not translatable',
                                $this->name,
                                $this->parentBuilder->getName()
                            )
                        );
                    }

                    throw new InvalidOptionsException(
                        sprintf(
                            'Parameter "%s" needs to be translatable, since its parent parameter "%s" is translatable',
                            $this->name,
                            $this->parentBuilder->getName()
                        )
                    );
                }

                return true;
            }
        );
    }
}
