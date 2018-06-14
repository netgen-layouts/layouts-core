<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\API\Values\Collection\Query;
use Netgen\BlockManager\Serializer\SerializerAwareTrait;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Version;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

final class CollectionQueryNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\BlockManager\API\Values\Collection\Query $query */
        $query = $object->getValue();

        $parameters = [];
        foreach ($query->getParameters() as $parameter) {
            $parameters[$parameter->getName()] = new VersionedValue($parameter, $object->getVersion());
        }

        return [
            'id' => $query->getId(),
            'collection_id' => $query->getCollectionId(),
            'type' => $query->getQueryType()->getType(),
            'locale' => $query->getLocale(),
            'is_translatable' => $query->isTranslatable(),
            'always_available' => $query->isAlwaysAvailable(),
            'parameters' => $this->serializer->normalize($parameters, $format, $context),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        if (!$data instanceof VersionedValue) {
            return false;
        }

        return $data->getValue() instanceof Query && $data->getVersion() === Version::API_V1;
    }
}
