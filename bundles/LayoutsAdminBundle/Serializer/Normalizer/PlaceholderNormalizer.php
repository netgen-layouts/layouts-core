<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Generator;
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
        $placeholder = $data->getValue();

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

        return $data->getValue() instanceof Placeholder;
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
     * @return \Generator<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View>
     */
    private function buildViewValues(iterable $values): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new View($value);
        }
    }
}
