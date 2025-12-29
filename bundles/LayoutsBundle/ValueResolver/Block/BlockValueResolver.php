<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Block;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Netgen\Layouts\API\Values\Status;
use Symfony\Component\Uid\Uuid;

final class BlockValueResolver extends ValueResolver
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    protected function getSourceAttributeNames(): array
    {
        return ['blockId'];
    }

    protected function getDestinationAttributeName(): string
    {
        return 'block';
    }

    protected function getSupportedClass(): string
    {
        return Block::class;
    }

    protected function loadValue(array $parameters): Block
    {
        /** @var string[]|null $locales */
        $locales = isset($parameters['locale']) ? [$parameters['locale']] : null;

        return match ($parameters['status']) {
            Status::Published => $this->blockService->loadBlock(Uuid::fromString($parameters['blockId']), $locales),
            default => $this->blockService->loadBlockDraft(Uuid::fromString($parameters['blockId']), $locales),
        };
    }
}
