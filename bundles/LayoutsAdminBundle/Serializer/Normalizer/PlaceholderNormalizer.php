<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaceholderNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @return array<string, mixed>
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        /** @var \Netgen\Layouts\API\Values\Block\Placeholder $placeholder */
        $placeholder = $data->value;

        $blocks = $this->buildViewValues($placeholder);

        return [
            'identifier' => $placeholder->identifier,
            'blocks' => $this->normalizer->normalize($blocks, $format, $context),
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        if (!$data instanceof Value) {
            return false;
        }

        return $data->value instanceof Placeholder;
    }

    /**
     * @return array<class-string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Builds the list of View objects for provided list of values.
     *
     * @param iterable<object> $values
     *
     * @return iterable<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View>
     */
    private function buildViewValues(iterable $values): iterable
    {
        foreach ($values as $key => $value) {
            yield $key => new View($value);
        }
    }
}
