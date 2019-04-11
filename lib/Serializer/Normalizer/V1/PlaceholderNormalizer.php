<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer\V1;

use Generator;
use Netgen\Layouts\API\Values\Block\Placeholder;
use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class PlaceholderNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\API\Values\Block\Placeholder $placeholder */
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
