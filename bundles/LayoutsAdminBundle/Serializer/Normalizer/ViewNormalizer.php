<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsAdminBundle\Serializer\Normalizer;

use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\Value;
use Netgen\Bundle\LayoutsAdminBundle\Serializer\Values\View;
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

    public function normalize($object, $format = null, array $context = []): array
    {
        /** @var array<string, mixed> $normalizedData */
        $normalizedData = $this->normalizer->normalize(
            new Value(
                $object->getValue(),
                $object->getStatusCode()
            ),
            $format,
            $context
        );

        if (!isset($context['disable_html']) || $context['disable_html'] !== true) {
            $normalizedData['html'] = $this->viewRenderer->renderValue(
                $object->getValue(),
                ViewInterface::CONTEXT_APP
            );
        }

        return $normalizedData;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof View;
    }
}
