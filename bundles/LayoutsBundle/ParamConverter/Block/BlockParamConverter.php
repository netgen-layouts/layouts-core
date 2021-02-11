<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Block;

use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;
use Netgen\Layouts\API\Service\BlockService;
use Netgen\Layouts\API\Values\Block\Block;
use Ramsey\Uuid\Uuid;

final class BlockParamConverter extends ParamConverter
{
    private BlockService $blockService;

    public function __construct(BlockService $blockService)
    {
        $this->blockService = $blockService;
    }

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
        /** @var string[] $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->blockService->loadBlock(Uuid::fromString($values['blockId']), $locales);
        }

        return $this->blockService->loadBlockDraft(Uuid::fromString($values['blockId']), $locales);
    }
}
