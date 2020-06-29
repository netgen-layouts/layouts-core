<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Exception\Transfer\SerializerException;
use Netgen\Layouts\Transfer\Descriptor;
use Psr\Container\ContainerInterface;

/**
 * Serializer serializes domain entities into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
final class Serializer implements SerializerInterface
{
    /**
     * @var \Netgen\Layouts\Transfer\Output\OutputVisitor
     */
    private $visitor;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $entityLoaders;

    public function __construct(
        OutputVisitor $visitor,
        ContainerInterface $entityLoaders
    ) {
        $this->visitor = $visitor;
        $this->entityLoaders = $entityLoaders;
    }

    public function serialize(string $type, array $entityIds): array
    {
        $data = $this->createBasicData();

        foreach ($this->getEntityLoader($type)->loadEntities($entityIds) as $entity) {
            $data['entities'][] = $this->visitor->visit($entity);
        }

        return $data;
    }

    /**
     * Creates the array with basic serialized data from provided type and version.
     *
     * @return array<string, mixed>
     */
    private function createBasicData(): array
    {
        return [
            '__version' => Descriptor::FORMAT_VERSION,
            'entities' => [],
        ];
    }

    /**
     * Returns the entity loader for provided entity type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Transfer\SerializerException If the entity loader does not exist or is not of correct type
     */
    private function getEntityLoader(string $type): EntityLoaderInterface
    {
        if (!$this->entityLoaders->has($type)) {
            throw SerializerException::noEntityLoader($type);
        }

        $entityLoader = $this->entityLoaders->get($type);
        if (!$entityLoader instanceof EntityLoaderInterface) {
            throw SerializerException::noEntityLoader($type);
        }

        return $entityLoader;
    }
}
