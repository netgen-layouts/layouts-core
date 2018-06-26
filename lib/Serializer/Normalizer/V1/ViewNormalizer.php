<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Serializer\Normalizer\V1;

use Netgen\BlockManager\Serializer\Normalizer;
use Netgen\BlockManager\Serializer\Values\VersionedValue;
use Netgen\BlockManager\Serializer\Values\View;
use Netgen\BlockManager\Serializer\Version;
use Netgen\BlockManager\View\RendererInterface;
use Netgen\BlockManager\View\ViewInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ViewNormalizer extends Normalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    private $viewRenderer;

    public function __construct(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * @param \Netgen\BlockManager\Serializer\Values\View $object
     * @param string $format
     * @param array $context
     *
     * @return mixed
     */
    public function normalize($object, $format = null, array $context = [])
    {
        /** @var array $normalizedData */
        $normalizedData = $this->normalizer->normalize(
            new VersionedValue(
                $object->getValue(),
                $object->getVersion(),
                $object->getStatusCode()
            ),
            $format,
            $context
        );

        if (!isset($context['disable_html']) || $context['disable_html'] !== true) {
            $normalizedData['html'] = $this->viewRenderer->renderValue(
                $object->getValue(),
                ViewInterface::CONTEXT_API,
                ['api_version' => $object->getVersion()]
            );
        }

        return $normalizedData;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof View && $data->getVersion() === Version::API_V1;
    }
}
