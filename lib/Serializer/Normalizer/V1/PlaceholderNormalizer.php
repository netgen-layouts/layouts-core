<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Block\Placeholder;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class PlaceholderNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Block\Placeholder $placeholder */
        $placeholder = $object->getValue();

        $blocks = [];
        foreach ($placeholder as $block) {
            $blocks[] = new View($block, $object->getVersion());
        }

        return [
            'identifier' => $placeholder->getIdentifier(),
            'blocks' => $this->serializer->normalize($blocks, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Placeholder && $data->getVersion() === Version::API_V1;
    }
}
