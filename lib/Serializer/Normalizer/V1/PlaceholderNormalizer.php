<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Generator;
use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaceholderNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */
        $placeholder = $object->getValue();

        $blocks = $this->buildViewValues($placeholder, $object->getVersion());

        return [
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->normalizer->normalize($blocks, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Placeholder && $data->getVersion() === Version::API_V1;
    }

    /**
     * Builds the list of View objects for provided list of values.
     */
    private function buildViewValues(iterable $values, int $version): Generator
    {
        foreach ($values as $key => $value) {
            yield $key => new View($value, $version);
        }
    }
}
