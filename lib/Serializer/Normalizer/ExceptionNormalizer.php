<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var bool
     */
    protected $outputDebugInfo = false;

    /**
     * Sets if the normalizer should output debugging information.
     *
     * @param bool $outputDebugInfo
     */
    public function setOutputDebugInfo($outputDebugInfo = false)
    {
        $this->outputDebugInfo = (bool)$outputDebugInfo;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param \Exception $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $data = array(
            'code' => $object->getCode(),
            'message' => $object->getMessage(),
        );

        if ($object instanceof HttpException) {
            $statusCode = $object->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $data['debug'] = array(
                'file' => $object->getFile(),
                'line' => $object->getLine(),
                'trace' => $object->getTraceAsString(),
            );
        }

        return $data;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data
     * @param string $format
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Exception;
    }
}
