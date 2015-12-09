<?php

namespace Netgen\BlockManager\Serializer\Normalizer;

use Netgen\BlockManager\Serializer\SerializableValue;
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
     * @param \Netgen\BlockManager\Serializer\SerializableValue $object
     * @param string $format
     * @param array $context
     *
     * @return array
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $exception = $object->value;

        $data = array(
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        );

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            if (isset(Response::$statusTexts[$statusCode])) {
                $data['status_code'] = $statusCode;
                $data['status_text'] = Response::$statusTexts[$statusCode];
            }
        }

        if ($this->outputDebugInfo) {
            $debugException = $exception;
            if ($exception->getPrevious() instanceof Exception) {
                $debugException = $exception->getPrevious();
            }

            $data['debug'] = array(
                'file' => $debugException->getFile(),
                'line' => $debugException->getLine(),
                'trace' => $debugException->getTrace(),
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
        if (!$data instanceof SerializableValue) {
            return false;
        }

        return $data->value instanceof Exception;
    }
}
