<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Input;

use Netgen\Layouts\Exception\Transfer\TransferException;
use Netgen\Layouts\Transfer\Input\Result\ErrorResult;
use Netgen\Layouts\Transfer\Input\Result\SuccessResult;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use Traversable;
use function file_get_contents;
use function json_decode;
use const JSON_THROW_ON_ERROR;

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
     * @var \Psr\Container\ContainerInterface
     */
    private $entityImporters;

    public function __construct(
        JsonValidatorInterface $jsonValidator,
        ContainerInterface $entityImporters
    ) {
        $this->jsonValidator = $jsonValidator;
        $this->entityImporters = $entityImporters;
    }

    public function importData(string $data): Traversable
    {
        $schema = (string) file_get_contents(self::SCHEMA_FILE);
        $this->jsonValidator->validateJson($data, $schema);

        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        foreach ($data['entities'] as $entityData) {
            try {
                $entity = $this->getEntityImporter($entityData['__type'])->importEntity($entityData);
                yield new SuccessResult($entityData['__type'], $entityData, $entity->getId(), $entity);
            } catch (Throwable $t) {
                yield new ErrorResult($entityData['__type'], $entityData, Uuid::fromString($entityData['id']), $t);
            }
        }
    }

    /**
     * Returns the entity importer for provided entity type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Transfer\TransferException If the entity importer does not exist or is not of correct type
     */
    private function getEntityImporter(string $type): EntityImporterInterface
    {
        if (!$this->entityImporters->has($type)) {
            throw TransferException::noEntityImporter($type);
        }

        $entityImporter = $this->entityImporters->get($type);
        if (!$entityImporter instanceof EntityImporterInterface) {
            throw TransferException::noEntityImporter($type);
        }

        return $entityImporter;
    }
}
