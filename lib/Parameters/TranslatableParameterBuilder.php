<?php

declare(strict_types=1);

namespace Netgen\Layouts\Parameters;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function sprintf;

final class TranslatableParameterBuilder extends ParameterBuilder
{
    protected function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefault('translatable', true);
        $optionsResolver->setRequired('translatable');
        $optionsResolver->setAllowedTypes('translatable', 'bool');

        $optionsResolver->setAllowedValues(
            'translatable',
            function (bool $value): bool {
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
                                $this->name ?? '',
                                $this->parentBuilder->getName() ?? '',
                            ),
                        );
                    }

                    throw new InvalidOptionsException(
                        sprintf(
                            'Parameter "%s" needs to be translatable, since its parent parameter "%s" is translatable',
                            $this->name ?? '',
                            $this->parentBuilder->getName() ?? '',
                        ),
                    );
                }

                return true;
            },
        );
    }
}
