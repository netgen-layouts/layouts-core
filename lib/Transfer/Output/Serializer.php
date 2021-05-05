<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Exception\Transfer\TransferException;
use Netgen\Layouts\Transfer\Descriptor;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

/**
 * Serializer serializes domain entities into hash representation, which can be
 * transferred through a plain text format, like JSON or XML.
 *
 * Hash format is either a scalar value, a hash array (associative array),
 * a pure numeric array or a nested combination of these.
 */
final class Serializer implements SerializerInterface
{
    private OutputVisitor $visitor;

    private ContainerInterface $entityHandlers;

    public function __construct(
        OutputVisitor $visitor,
        ContainerInterface $entityHandlers
    ) {
        $this->visitor = $visitor;
        $this->entityHandlers = $entityHandlers;
    }

    public function serialize(array $entityIds): array
    {
        $data = $this->createBasicData();

        foreach ($entityIds as $entityId => $type) {
            $data['entities'][] = $this->visitor->visit(
                $this->getEntityHandler($type)->loadEntity(Uuid::fromString($entityId)),
            );
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
     * Returns the entity handler for provided entity type from the collection.
     *
     * @throws \Netgen\Layouts\Exception\Transfer\TransferException If the entity handler does not exist or is not of correct type
     */
    private function getEntityHandler(string $type): EntityHandlerInterface
    {
        if (!$this->entityHandlers->has($type)) {
            throw TransferException::noEntityHandler($type);
        }

        $entityHandler = $this->entityHandlers->get($type);
        if (!$entityHandler instanceof EntityHandlerInterface) {
            throw TransferException::noEntityHandler($type);
        }

        return $entityHandler;
    }
}
