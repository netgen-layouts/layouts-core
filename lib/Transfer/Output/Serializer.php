<?php

declare(strict_types=1);

namespace Netgen\Layouts\Transfer\Output;

use Netgen\Layouts\Exception\Transfer\TransferException;
use Netgen\Layouts\Transfer\EntityHandlerInterface;
use Netgen\Layouts\Transfer\EntityType;
use Psr\Container\ContainerInterface;
use Symfony\Component\Uid\Uuid;

final class Serializer implements SerializerInterface
{
    public function __construct(
        private OutputVisitor $visitor,
        private ContainerInterface $entityHandlers,
    ) {}

    public function serialize(array $entityIds): array
    {
        $data = $this->createBasicData();

        foreach ($entityIds as $entityId => $type) {
            $entityType = EntityType::from($type);

            $location = match ($entityType) {
                EntityType::Layout => 'layouts',
                EntityType::RuleGroup => 'rule_groups',
                EntityType::Rule => 'rules',
                EntityType::Role => 'roles',
            };

            $data[$location][] = $this->visitor->visit(
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
            '__version' => self::FORMAT_VERSION,
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
