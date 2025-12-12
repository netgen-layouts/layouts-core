<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ValueResolver\Block;

use Netgen\Bundle\LayoutsBundle\ValueResolver\ValueResolver;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Ramsey\Uuid\Uuid;

final class BlockValueResolver extends ValueResolver
{
    public function __construct(
        private BlockService $blockService,
    ) {}

    public function getSourceAttributeNames(): array
    {
        return ['blockId'];
    }

    public function getDestinationAttributeName(): string
    {
        return 'block';
    }

    public function getSupportedClass(): string
    {
        return Block::class;
    }

    public function loadValue(array $values): Block
    {
        /** @var string[]|null $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        return match ($values['status']) {
            self::STATUS_PUBLISHED => $this->blockService->loadBlock(Uuid::fromString($values['blockId']), $locales),
            default => $this->blockService->loadBlockDraft(Uuid::fromString($values['blockId']), $locales),
        };
    }
}
