<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Exception;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var bool
     */
    private $outputDebugInfo = false;

    /**
     * Sets if the normalizer should output debugging information.
     *
     * @param bool $outputDebugInfo
     */
    public function setOutputDebugInfo($outputDebugInfo = false)
    {
        $this->outputDebugInfo = (bool) $outputDebugInfo;
    }

    public function normalize($object, $format = null, array $context = array())
    {
        $data = array(
            'code' => $object->getCode(),
            'message' => $object->getMessage(),
        );

        if ($object instanceof HttpExceptionInterface) {
            $statusCode = $object->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $debugException = $object;
            if ($object->getPrevious() instanceof Exception) {
                $debugException = $object->getPrevious();
            }

            $debugException = FlattenException::create($debugException);

            $data['debug'] = array(
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $debugException->getTrace(),
            );
        }

        return $data;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Exception;
    }
}
