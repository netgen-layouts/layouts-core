<?php

namespace Netgen\BlockManager\Transfer\Input;

use Exception;
use Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler;
use Netgen\BlockManager\Transfer\Input\Result\ErrorResult;
use Netgen\BlockManager\Transfer\Input\Result\SuccessResult;
use Throwable;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
final class Importer implements ImporterInterface
{
    /**
     * @var \Netgen\BlockManager\Transfer\Input\JsonValidatorInterface
     */
    private $jsonValidator;

    /**
     * @var \Netgen\BlockManager\Transfer\Input\DataHandler\LayoutDataHandler
     */
    private $layoutDataHandler;

    /**
     * The path to the root schema directory.
     *
     * @var string
     */
    private static $schemaFile = __DIR__ . '/../../../resources/schemas/import.json';

    public function __construct(JsonValidatorInterface $jsonValidator, LayoutDataHandler $layoutDataHandler)
    {
        $this->jsonValidator = $jsonValidator;
        $this->layoutDataHandler = $layoutDataHandler;
    }

    public function importData($data)
    {
        $schema = file_get_contents(self::$schemaFile);
        $this->jsonValidator->validateJson($data, $schema);

        $data = json_decode($data, true);

        foreach ($data['entities'] as $entityData) {
            try {
                $layout = $this->layoutDataHandler->createLayout($entityData);
                yield new SuccessResult('layout', $entityData, $layout->getId(), $layout);
            } catch (Throwable $t) {
                yield new ErrorResult('layout', $entityData, $t);
            } catch (Exception $e) {
                yield new ErrorResult('layout', $entityData, $e);
            }
        }
    }
}
