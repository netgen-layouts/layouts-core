<?php

declare(strict_types=1);

namespace Netgen\Layouts\Serializer\Normalizer\V1;

use Netgen\Layouts\Serializer\Values\VersionedValue;
use Netgen\Layouts\Serializer\Values\View;
use Netgen\Layouts\Serializer\Version;
use Netgen\Layouts\View\RendererInterface;
use Netgen\Layouts\View\ViewInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ViewNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    /**
     * @var \Netgen\Layouts\View\RendererInterface
     */
    private $viewRenderer;

    public function __construct(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        /** @var \Netgen\Layouts\Serializer\Values\View $object */
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
                ViewInterface::CONTEXT_API
            );
        }

        return $normalizedData;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof View && $data->getVersion() === Version::API_V1;
    }
}
