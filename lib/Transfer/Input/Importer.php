<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Throwable;
use Traversable;

/**
 * Importer creates Netgen Layouts entities from the serialized JSON data.
 */
final class Importer implements ImporterInterface
{
    /**
     * The path to the root schema directory.
     */
    private const SCHEMA_FILE = __DIR__ . '/../../../resources/schemas/import.json';

    /**
     * @var \Netgen\Layouts\Transfer\Input\JsonValidatorInterface
     */
    private $jsonValidator;

    /**
     * @var \Netgen\Layouts\Transfer\Input\DataHandler\LayoutDataHandler
     */
    private $layoutDataHandler;

    public function __construct(JsonValidatorInterface $jsonValidator, LayoutDataHandler $layoutDataHandler)
    {
        $this->jsonValidator = $jsonValidator;
        $this->layoutDataHandler = $layoutDataHandler;
    }

    public function importData(string $data): Traversable
    {
        $schema = (string) file_get_contents(self::SCHEMA_FILE);
        $this->jsonValidator->validateJson($data, $schema);

        $data = json_decode($data, true);

        foreach ($data['entities'] as $entityData) {
            try {
                $layout = $this->layoutDataHandler->createLayout($entityData);
                yield new SuccessResult('layout', $entityData, $layout->getId(), $layout);
            } catch (Throwable $t) {
                yield new ErrorResult('layout', $entityData, $t);
            }
        }
    }
}
