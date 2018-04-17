<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\Values\FormView;
use Netgen\BlockManager\View\RendererInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class FormViewNormalizer implements NormalizerInterface
{
    /**
     * @var \Netgen\BlockManager\View\RendererInterface
     */
    private $viewRenderer;

    public function __construct(RendererInterface $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        return [
            'form' => $this->viewRenderer->renderValue(
                $object->getValue(),
                $object->getContext(),
                [
                    'api_version' => $object->getVersion(),
                ] + $object->getViewParameters()
            ),
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FormView;
    }
}
