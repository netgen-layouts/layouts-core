<?php

declare(strict_types=1);

namespace Netgen\Layouts\Persistence\Values\Block;

use Netgen\Layouts\Utils\HydratorTrait;

final class BlockUpdateStruct
{
    use HydratorTrait;

    /**
     * New view type of the block.
     */
    public ?string $viewType = null;

    /**
     * New item view type of the block.
     */
    public ?string $itemViewType = null;

    /**
     * New human readable name of the block.
     */
    public ?string $name = null;

    /**
     * Flag indicating if the block will be always available.
     */
    public ?bool $alwaysAvailable = null;

    /**
     * Flag indicating if the block will be translatable.
     */
    public ?bool $isTranslatable = null;

    /**
     * New block configuration.
     *
     * @var array<string, array<string, mixed>>|null
     */
    public ?array $config = null;
}
