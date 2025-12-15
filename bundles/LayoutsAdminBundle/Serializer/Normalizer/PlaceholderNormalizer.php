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

        $blocks = $this->buildViewValues($placeholder->blocks);

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

    public function getSupportedTypes(?string $format): array
    {
        return [
            Value::class => false,
        ];
    }

    /**
     * Builds the list of View objects for provided list of blocks.
     *
     * @param iterable<\Netgen\Layouts\API\Values\Block\Block> $blocks
     *
     * @return iterable<array-key, \Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View>
     */
    private function buildViewValues(iterable $blocks): iterable
    {
        foreach ($blocks as $key => $block) {
            yield $key => new View($block);
        }
    }
}
