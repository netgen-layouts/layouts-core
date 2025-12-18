<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Layouts\Parameters\Parameter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ParameterNormalizer implements NormalizerInterface
{
    /**
     * @return int|string|mixed[]|float|bool|null
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): int|string|array|float|bool|null
    {
        /** @var \Netgen\Layouts\Parameters\Parameter $parameter */
        $parameter = $data->value;
        $parameterDefinition = $parameter->parameterDefinition;

        return $parameterDefinition->type->toHash($parameterDefinition, $parameter->value);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Parameter;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }
}
