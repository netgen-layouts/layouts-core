<?php

declare(strict_types=1);

namespace Netgen\Bundle\LayoutsBundle\ParamConverter\Block;

use Netgen\BlockManager\API\Service\BlockService;
use Netgen\BlockManager\API\Values\Block\Block;
use Netgen\BlockManager\API\Values\Value;
use Netgen\Bundle\LayoutsBundle\ParamConverter\ParamConverter;

final class BlockParamConverter extends ParamConverter
{
    /**
     * @var \Netgen\BlockManager\API\Service\BlockService
     */
    private $blockService;

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

    public function loadValue(array $values): Value
    {
        /** @var string[] $locales */
        $locales = isset($values['locale']) ? [$values['locale']] : null;

        if ($values['status'] === self::STATUS_PUBLISHED) {
            return $this->blockService->loadBlock($values['blockId'], $locales);
        }

        return $this->blockService->loadBlockDraft($values['blockId'], $locales);
    }
}
